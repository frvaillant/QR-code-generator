<?php
namespace App\Form;

class FormManager
{
    private string $url         = '';
    private int    $size        = 256;
    private string $color       = '#000000';
    private string $lightColor  = '#FFFFFF';
    private string $quality     = 'H';
    private ?array $errors      = null;
    public  bool   $isSubmitted = false;

    const MAX_SIZE              = 1024;
    const URL_ERROR_MESSAGE     = '<p>url absente ou invalide (peut être avez vous oublié de mettre http:// ou https:// devant)</p>';
    const SIZE_ERROR_MESSAGE    = '<p>La taille doit être un nombre entier inférieur ou égal à <strong>%s</strong>. Vous avez choisi <strong>%s</strong></p>';
    const COLOR_ERROR_MESSAGE   = '<p>La couleur spécifiée est invalide.<br>Exemple : <strong>#AF00FE</strong>.<br>Vous avez choisi <strong>%s</strong></p>';


    /**
     * @param null $postData
     * Tested
     */
    public function __construct($postData = null)
    {
        if($postData && count($postData) > 0) {
            $this->isSubmitted = true;
        }

        if($this->isSubmitted) {
            if (!$this->hasParam($postData, 'url') || !$this->isValidUrl($postData['url'])) {
                $this->errors['url'] = self::URL_ERROR_MESSAGE;
            }

            if ($this->hasParam($postData, 'size') && !$this->isValidSize($postData['size'])) {
                $this->errors['size'] = sprintf(self::SIZE_ERROR_MESSAGE, self::MAX_SIZE, $postData['size']);
            }

            if ($this->hasParam($postData, 'color') && !$this->isValidColor($postData['color'])) {
                $this->errors['color'] = sprintf(self::COLOR_ERROR_MESSAGE, $postData['color']);
            }

            if ($this->hasParam($postData, 'light_color') && !$this->isValidColor($postData['light_color'])) {
                $this->errors['light_color'] = sprintf(self::COLOR_ERROR_MESSAGE, $postData['light_color']);
            }

            if ($this->hasParam($postData, 'quality') && !$this->isValidQuality($postData['quality'])) {
                $this->errors['quality'] = sprintf(self::COLOR_ERROR_MESSAGE, $postData['quality']);
            }

            if ($this->hasNotErrors()) {
                $this->setUrl($postData['url']);
                if ($this->hasParam($postData, 'size')) {
                    $this->setSize($postData['size']);
                }
                if ($this->hasParam($postData, 'color')) {
                    $this->setColor($postData['color']);
                }
                if ($this->hasParam($postData, 'light_color')) {
                    $this->setLightColor($postData['light_color']);
                }
                if ($this->hasParam($postData, 'quality')) {
                    $this->setQuality($postData['quality']);
                }
            }
        }
    }

    public function hasNotErrors(): bool
    {
        return null === $this->errors;
    }

    public function isFilled(): bool
    {
        return $this->getUrl() !== '';
    }

    public function getErrors(): ?array
    {
        return $this->errors;
    }

    /**
     * @param $data
     * @return bool
     */
    private function hasParam($data, $param): bool
    {
        return isset($data[$param]);
    }


    /**
     * @param $url
     * @return bool
     * Tested
     */
    private function isValidUrl($url): bool
    {
        $path = parse_url($url, PHP_URL_PATH) ?? '';

        $encoded_path = array_map('urlencode', explode('/', $path));
        $url = str_replace($path, implode('/', $encoded_path), $url);

        $url = filter_var($url, FILTER_SANITIZE_URL);
        return filter_var($url, FILTER_VALIDATE_URL);
    }

    /**
     * @param $size
     * @return bool
     */
    private function isValidSize($size): bool
    {
        return filter_var($size, FILTER_VALIDATE_INT) && $size <= self::MAX_SIZE;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url): void
    {
        $url = filter_var($url, FILTER_SANITIZE_URL);
        $this->url = $url;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * @param int $size
     */
    public function setSize(int $size): void
    {
        $this->size = $size;
    }

    /**
     * @return string
     */
    public function getColor(): string
    {
        return $this->color;
    }

    /**
     * @param string $color
     */
    public function setColor(string $color): void
    {
        $this->color = $color;
    }

    /**
     * @return string
     */
    public function getLightColor(): string
    {
        return $this->lightColor;
    }

    /**
     * @param string $lightColor
     */
    public function setLightColor(string $lightColor): void
    {
        $this->lightColor = $lightColor;
    }


    private function isValidColor($color): bool
    {
        return preg_match('/#([a-zA-Z0-9]{6})/', $color);
    }

    /**
     * @return string
     */
    public function getQuality(): string
    {
        return $this->quality;
    }

    /**
     * @param string $quality
     */
    public function setQuality(string $quality): void
    {
        $this->quality = $quality;
    }

    private function isValidQuality($quality): bool
    {
        return in_array($quality, ['L', 'M', 'Q', 'H']);
    }







}