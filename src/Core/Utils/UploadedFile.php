<?php

namespace App\Core\Utils;

use Exception;

class UploadedFile
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $type;
    /**
     * @var string
     */
    private $ext;
    /**
     * @var string
     */
    private $tmp;
    /**
     * @var int
     */
    private $error;
    /**
     * @var int
     */
    private $size;

    private static $types = [
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',

        'pdf' => 'application/pdf',
        'odt' => 'application/vnd.oasis.opendocument.text',
        'txt' => 'text/plain',

        'mp3' => 'music/mp3',

        'mp4' => 'video/mp4',
        'mov' => '',
    ];

    public const DOCUMENT = ['pdf', 'txt', 'odt'];
    public const IMAGE = ['jpg', 'jpeg', 'png'];
    public const VIDEO = ['mp4', 'mov'];
    public const MUSIC = ['mp3'];

    /**
     * @param array $array
     * @return array
     */
    private static function rearrangeUploadArray(array $array): array
    {
        if (!is_array(reset($array))) {
            return $array;
        }

        $rearranged = [];
        foreach ($array as $property => $values) {
            foreach ($values as $key => $value) {
                $rearranged[$key][$property] = $value;
            }
        }

        foreach ($rearranged as &$value) {
            $value = self::rearrangeUploadArray($value);
        }

        return $rearranged;
    }

    /**
     * @param array $array
     * @return bool
     */
    private static function isSpecs(array $array): bool
    {
        $keys = array_keys($array);
        $needed = ['name', 'type', 'tmp_name', 'error', 'size'];

        return count($keys) === count($needed)
            && count(array_diff(
                $keys,
                $needed
            )) === 0;
    }

    /**
     * @param array $specs
     * @return ParameterBag|UploadedFile
     */
    private static function createFromFile(array $specs)
    {
        $p = new ParameterBag();

        if (self::isSpecs($specs)) {
            return new UploadedFile(
                $specs['name'],
                $specs['type'],
                $specs['tmp_name'],
                $specs['error'],
                $specs['size']
            );
        } else {
            foreach ($specs as $key => $value) {
                $p->set($key, self::createFromFile($value));
            }
            return $p;
        }
    }

    /**
     * @param array $array
     * @return ParameterBag <UploadedFile>
     */
    public static function fromArray(array $array): ParameterBag
    {
        $filebag = new ParameterBag();
        foreach ($array as $input => $specs) {
            $filebag->set($input, self::createFromFile(self::rearrangeUploadArray($specs)));
        }
        return $filebag;
    }

    public function __construct(string $name, string $type, string $tmp, int $error, int $size)
    {
        $this->name = $this->sanitizeFilename($name);
        $this->type = $type;
        $this->ext = pathinfo($name)['extension'] ?? '';
        $this->tmp = $tmp;
        $this->error = $error;
        $this->size = $size;
    }

    private function sanitizeFilename(string $name): string
    {
        $infos = pathinfo($name);
        return preg_replace("/[^\d\w_.]/", "_", $infos['filename']);
    }

    private function prepareFileName(string $dir, string $name, string $ext)
    {
        if (strpos($name, DS) !== false) {
            throw new InvalidArgumentException(sprintf("%s isn't a valid filename", $name));
        }

        $filename = $name . '.' . $ext;
        $filepath = $dir . $filename;
        $i = 0;
        while (file_exists($filepath)) {
            $filename = $name . '_' . ++$i . '.' . $ext;
            $filepath = $dir . $filename;
        }

        return $filename;
    }

    /**
     * @param array $allowedExtensions @see UploadedFile::const
     * @return bool
     */
    public function match(array $allowedExtensions): bool
    {
        return (bool)preg_match('/' . implode('|', $allowedExtensions) . '/', $this->ext);
    }

    /**
     * @return bool
     */
    public function valid(): bool
    {
        return $this->size > 0
            && $this->error === UPLOAD_ERR_OK
            && is_uploaded_file($this->tmp)
            && array_key_exists($this->ext, self::$types)
            && self::$types[$this->ext] === $this->type;
    }

    /**
     * move the file to the destination
     * @param string $directory
     * @param null|string $name
     * @return string the name of the image moved
     * @throws Exception
     */
    public function move(string $directory, ?string $name = null): string
    {
        if (!$this->valid()) {
            throw new Exception("UploadedFile::move The file isn't valid, aborting move");
        }

        if ($name === null) {
            $name = $this->name;
        } else {
            $name = $this->sanitizeFilename($name);
        }

        if ($directory[-1] !== DS) {
            $directory .= DS;
        }

        $filename = $this->prepareFileName($directory, $name, $this->ext);
        $filepath = $directory . $filename;

        if (!@move_uploaded_file($this->tmp, $filepath)) {
            $error = error_get_last();
            throw new Exception(sprintf('Could not move the file "%s" to "%s" (%s)',
                $this->tmp, $filepath, strip_tags($error['message'])
            ));
        }

        @chmod($filepath, 0666 & ~umask());

        return $filename;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getExt(): string
    {
        return $this->ext;
    }

    /**
     * @return int
     */
    public function getError(): int
    {
        return $this->error;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

}