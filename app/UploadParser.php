<?php

namespace App;

use App\Exceptions\UploadParserException;
use Log;
use ZipArchive;

class UploadParser
{
    /**
     * @var ZipArchive
     */
    protected $zipFile = null;

    /**
     * Cached/Parsed song data.
     *
     * @var array
     */
    protected $songData = [];

    /**
     * ZipArchive file index.
     *
     * @var array
     */
    protected $indexData = [];

    /**
     * @var string
     */
    protected $file;

    /**
     * UploadParser constructor.
     *
     * @param $file
     *
     * @throws UploadParserException
     */
    public function __construct($file)
    {
        Log::debug('parser started');
        $this->file = $file;
        $this->openZip();
        $this->indexData = $this->createZipIndex();
        Log::debug('loaded ' . $file);
    }


    public function __destruct()
    {
        $this->closeZip();
    }

    /**
     * @param bool $noCache
     *
     * @return array
     * @throws UploadParserException
     */
    public function getSongData($noCache = false)
    {
        if ($noCache || empty($this->songData)) {
            Log::debug('force parse song data');
            $this->songData = $this->parseSongFromChopIt();
        }

        return $this->songData;
    }

    /**
     * Parse the zip file for song metadata for Chop It
     *
     * @return array
     * @throws UploadParserException
     */
    protected function parseSongFromChopIt()
    {
        $songData = [];

        $info = $this->readFromZip('SongInfo.json');
        // workaround for info.json files with non UTF8 encoded characters
        // remove BOM
        $info = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $info);
        $info = json_decode($info, true);

        if ($info) {
            Log::debug('found SongInfo.json');

            $songData['songName'] = trim($info['songName']);
            $songData['artistName'] = trim($info['artistName']);

            // XXX: What to do for author name?
            $songData['authorName'] = 'test';

            $songData['beatmaps'] = [];
            $beatmaps = $this->findFilenamesInZip('beatmap');

            $hashBase = '';
            foreach ($beatmaps as $beatmap) {
                // Hash based on contents of maps
                $hashBase .= $this->zipFile->getFromName($beatmap);

                // Add filename without path for display
                $songData['beatmaps'][] = array_slice(explode('/',  $beatmap), -1)[0];
            }

            if ($this->zipHasFile('cover.png')) {
                $songData['coverType'] = 'png';
                $songData['coverData'] = base64_encode($this->readFromZip('cover.png'));
            } else if ($this->zipHasFile('cover.jpg')) {
                $songData['coverType'] = 'jpg';
                $songData['coverData'] = base64_encode($this->readFromZip('cover.jpg'));
            } else {
                // XXX: Cover images not required for Chop It.  Use default logo.
                $songData['coverType'] = '';
                $songData['coverData'] = '';
            }

            if ($hashBase) {
                $songData['hashMD5'] = md5($hashBase);
                $songData['hashSHA1'] = sha1_file($this->file);
            } else {
                // without hashes the parsing failed
                throw new UploadParserException('Song hash could not be calculated!');
            }
        }

        return $songData;
    }

    /**
     * @param $fileName
     *
     * @return bool
     */
    protected function zipHasFile($fileName): bool
    {
        Log::debug('check zip index for file: ' . strtolower($fileName));
        Log::debug(array_keys($this->indexData));
        return array_key_exists(strtolower($fileName), $this->indexData);
    }

    /**
     * Read a single file from the zip.
     *
     * @param string $indexName
     *
     * @return string
     * @throws UploadParserException
     */
    protected function readFromZip(string $indexName): string
    {
        // every index is lowercase
        $indexName = strtolower($indexName);
        Log::debug('search index for: ' . $indexName);
        if ($this->zipFile && array_key_exists($indexName, $this->indexData)) {
            Log::debug('found index ' . $this->indexData[$indexName]['index']);
            return $this->zipFile->getFromIndex($this->indexData[$indexName]['index']);
        }
        throw new UploadParserException('Invalid index (' . $indexName . ')');
    }

    /**
     * Create a file index for the opened zip file.
     *
     * @return array
     * @throws UploadParserException
     */
    protected function createZipIndex(): array
    {
        if ($this->zipFile) {
            $index = [];
            for ($i = 0; $i < $this->zipFile->numFiles; $i++) {
                $stat = $this->zipFile->statIndex($i);
                // do not index empty files
                if ($stat['size']) {
                    $index[strtolower(basename($stat['name']))] = $stat;
                }
            }
            Log::debug($index);
            return $index;
        }

        throw new UploadParserException('Cannot create index. Zip file not open');
    }

    /**
     * Return file names that match search string.
     *
     * @param $searchString
     * @return array
     */
    protected function findFilenamesInZip(string $searchString): array
    {
        $filenames = [];
        if ($this->zipFile) {
            for($i = 0; $i < $this->zipFile->numFiles; $i++) {
                $curFileName = $this->zipFile->getNameIndex($i);
                if (strpos($curFileName, $searchString) != false) {
                    $fileNames[] = $curFileName;
                }
            }
        }

        return $fileNames;
    }

    /**
     * Open a ZipArchive.
     *
     * @throws UploadParserException
     */
    protected function openZip()
    {
        $zip = new ZipArchive();
        if (!$this->file || !$zip->open($this->file)) {
            throw new UploadParserException('Cannot open zip file (' . $this->file . ')');
        }
        $this->zipFile = $zip;

    }

    /**
     * Close open ZipArchive handle (if one exists).
     */
    protected function closeZip()
    {
        if ($this->zipFile && $this->zipFile->close()) {
            $this->zipFile = null;
        }
    }
}
