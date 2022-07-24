<?php

/**
 * 文章配图生成类
 */
class Article_With_Pictures_Api
{
    /**
     * @var int 图片宽度
     */
    private $width;

    /**
     * @var int 图片高度
     */
    private $height;

    /**
     * @var array 图片背景颜色RGB
     */
    private $backgroundRGB = array(0, 0, 0);

    /**
     * @var array 文字颜色RGB
     */
    private $textRGB = array(255, 255, 255);

    /**
     * @var string 文字
     */
    private $text;

    /**
     * @var float 字体大小
     */
    private $fontSize = 20;

    /**
     * @var string 字体文件路径
     */
    private $fontFile;

    /**
     * @var bool 是否开启多行文字。如果文字一行显示不了，则多行显示
     */
    private $isMultiLine = false;

    /**
     * @var string 单行显示的文字如果超了，截取的字符串使用它来代替
     */
    private $singleLineText = '...';

    /**
     * @var string 错误信息
     */
    private $error = '';

    /**
     * 获取图片宽度
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * 设置图片宽度
     * @param int $width
     */
    public function setWidth($width)
    {
        $this->width = $width;
    }

    /**
     * 获取图片高度
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * 设置图片高度
     * @param int $height
     */
    public function setHeight($height)
    {
        $this->height = $height;
    }

    /**
     * 获取背景颜色的RGB
     * @return array
     */
    public function getBackgroundRGB()
    {
        return $this->backgroundRGB;
    }

    /**
     * 设置背景颜色的RGB
     * @param array $backgroundRGB
     */
    public function setBackgroundRGB($backgroundRGB)
    {
        $this->backgroundRGB = $backgroundRGB;
    }

    /**
     * 获取文字颜色的RGB
     * @return array
     */
    public function getTextRGB()
    {
        return $this->textRGB;
    }

    /**
     * 设置文字颜色的RGB
     * @param array $textRGB
     */
    public function setTextRGB($textRGB)
    {
        $this->textRGB = $textRGB;
    }

    /**
     * 获取文字
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * 设置文字
     * @param string $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * 获取文字大小
     * @return float
     */
    public function getFontSize()
    {
        return $this->fontSize;
    }

    /**
     * 设置文字大小
     * @param float $fontSize
     */
    public function setFontSize($fontSize)
    {
        $this->fontSize = $fontSize;
    }

    /**
     * 获取字体文件
     * @return string
     */
    public function getFontFile()
    {
        return $this->fontFile;
    }

    /**
     * 设置字体文件
     * @param string $fontFile
     */
    public function setFontFile($fontFile)
    {
        $this->fontFile = $fontFile;
    }

    /**
     * 判断是否为多行文字
     * @return bool
     */
    public function isMultiLine()
    {
        return $this->isMultiLine;
    }

    /**
     * 设置为多行文字
     * @param bool $isMultiLine
     */
    public function setIsMultiLine($isMultiLine)
    {
        $this->isMultiLine = $isMultiLine;
    }

    /**
     * 获取单行文字超过后替换的截取字符串
     * @return string
     */
    public function getSingleLineText()
    {
        return $this->singleLineText;
    }

    /**
     * 设置单行文字超过后替换的截取字符串
     * @param string $singleLineText
     */
    public function setSingleLineText($singleLineText)
    {
        $this->singleLineText = $singleLineText;
    }

    /**
     * 获取错误信息
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * 设置错误信息
     * @param string $error
     */
    public function setError($error)
    {
        $this->error = $error;
    }

    /**
     * 构造方法
     * @param int $width 图片宽度
     * @param int $height 图片高度
     * @param string $text 图片文字
     * @param string $fontFile 字体文件
     */
    public function __construct($width, $height, $text, $fontFile)
    {
        $this->setWidth($width);
        $this->setHeight($height);
        $this->setText($text);
        $this->setFontFile($fontFile);
    }

    /**
     * 获取颜色的RGB
     * @param string $hexColor 颜色
     * @return bool|array
     */
    public function getRGB($hexColor)
    {
        if (!preg_match('/^#[0-9a-f]{3,6}$/i', $hexColor)) {
            $this->setError('颜色值不符合规则');
            return false;
        }
        $hexColor = substr($hexColor, 1);
        $len = strlen($hexColor);
        if (3 === $len) {
            return array(
                hexdec($hexColor[0] . $hexColor[0]),
                hexdec($hexColor[1] . $hexColor[1]),
                hexdec($hexColor[2] . $hexColor[2])
            );
        } else if (6 === $len) {
            return array(
                hexdec($hexColor[0] . $hexColor[1]),
                hexdec($hexColor[2] . $hexColor[3]),
                hexdec($hexColor[4] . $hexColor[5])
            );
        } else {
            $this->setError('颜色值长度不符合规则');
            return false;
        }
    }

    /**
     * 获取图片
     * @param int $quality 图片质量。范围从0（最低质量，最小文件体积）到100 （最好质量, 最大文件体积）。
     * @return array|bool
     */
    public function getImage($quality = 100)
    {
        $im = imagecreatetruecolor($this->width, $this->height);
        if (false === $im) {
            $this->setError('创建真彩色图像失败');
            return false;
        }
        imagefilledrectangle($im, 0, 0, imagesx($im) - 1, imagesy($im) - 1, imagecolorallocate($im, $this->backgroundRGB[0], $this->backgroundRGB[1], $this->backgroundRGB[2]));
        // 获取当前字体下的每个文字宽度和高度
        $wordBbox = imagettfbbox($this->fontSize, 0, $this->fontFile, '果');
        $wordWidth = abs($wordBbox[0] - $wordBbox[4]) + abs($wordBbox[0] * 2);
        $wordHeight = abs($wordBbox[1] - $wordBbox[5]) + abs($wordBbox[1] * 2);
        // 图片上每行最多文字
        $lineTextNum = floor($this->width / $wordWidth);
        // 图片上最多文字行数
        $lineNum = floor($this->height / $wordHeight);
        $text = $this->text;
        $textColor = imagecolorallocate($im, $this->textRGB[0], $this->textRGB[1], $this->textRGB[2]);
        if ($this->isMultiLine()) {
            // 多行文字
            $line = ceil(mb_strlen($text, 'utf-8') / $lineTextNum);
            // 图片文字行数超了，就截取
            if ($line > $lineNum) {
                $line = $lineNum;
                $text = mb_substr($text, 0, $lineTextNum * $line - 3, 'utf-8') . $this->singleLineText;
            }
            $startLen = mb_strlen($text, 'utf-8') % $lineTextNum;
            if (0 === $startLen) {
                $startLen = $lineTextNum;
            }
            for ($i = 1; $i <= $line; $i++) {
                if (1 === $i) {
                    $t = mb_substr($text, 0, $startLen, 'utf-8');
                } else {
                    $t = mb_substr($text, $startLen + ($i - 2) * $lineTextNum, $lineTextNum, 'utf-8');
                }
                $bbox = imagettfbbox($this->fontSize, 0, $this->fontFile, $t);
                $x = $bbox[0] + ($this->width / 2) - ($bbox[4] / 2);
                $y = ($this->height / 2) - ($line * $wordHeight / 2) - $bbox[1];
                imagettftext($im, $this->fontSize, 0, (int)$x, (int)($y + $i * $wordHeight), $textColor, $this->fontFile, $t);
            }
        } else {
            // 单行文字
            if (mb_strlen($text, 'utf-8') > $lineTextNum) {
                $text = mb_substr($text, 0, $lineTextNum - 3, 'utf-8') . $this->singleLineText;
            }
            $bbox = imagettfbbox($this->fontSize, 0, $this->fontFile, $text);
            $x = $bbox[0] + ($this->width / 2) - ($bbox[4] / 2);
            $y = $bbox[1] + ($this->height / 2) - ($bbox[5] / 2);
            imagettftext($im, $this->fontSize, 0, (int)$x, (int)$y, $textColor, $this->fontFile, $text);
        }
        $upload_dir = wp_upload_dir();
        $img_filename = 'article_with_pictures_plugin_' . Article_With_Pictures_Plugin::get_image_key($this->getText());
        if (function_exists('imagewebp')) {
            $img_filename = $img_filename . '.webp';
            imagewebp($im, $upload_dir['path'] . '/' . $img_filename, $quality);
        } else {
            $img_filename = $img_filename . '.png';
            imagepng($im, $upload_dir['path'] . '/' . $img_filename);
        }
        if (!file_exists($upload_dir['path'] . '/' . $img_filename)) {
            return false;
        }
        imagedestroy($im);
        return array(
            'path' => $upload_dir['path'] . '/' . $img_filename,
            'url' => $upload_dir['url'] . '/' . basename($img_filename)
        );
    }
}