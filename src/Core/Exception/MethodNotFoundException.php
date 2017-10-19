<?php

/*
 * This file is part of the Prophecy.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Core\Exception;

use Exception;

class MethodNotFoundException extends Exception
{
    private $method;

    /**
     * @param string $message
     * @param string $method
     */
    public function __construct(string $message, string $method)
    {
        parent::__construct($message);

        $this->method = $method;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }
}
