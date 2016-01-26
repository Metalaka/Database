<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2016, Hoa community. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the Hoa nor the names of its contributors may be
 *       used to endorse or promote products derived from this software without
 *       specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDERS AND CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 */

namespace Hoa\Database\Query;

/**
 * Class \Hoa\Database\Query\Join.
 *
 * Build a JOIN clause.
 *
 * @copyright  Copyright © 2007-2016 Hoa community
 * @license    New BSD License
 */
class Join
{
    /**
     * Parent query.
     *
     * @var \Hoa\Database\Query\Select
     */
    protected $_parent = null;

    /**
     * Reference the FROM entry of the parent (simulate “friends”).
     *
     * @var string
     */
    protected $_from   = null;



    /**
     * Constructor.
     *
     * @param   \Hoa\Database\Query\Select  $parent    Parent query.
     * @param   array                       $from      FROM entry (“friends”).
     * @return  void
     */
    public function __construct(Select $parent, array &$from)
    {
        $this->_parent = $parent;
        $this->_from   = &$from;
        end($this->_from);

        return;
    }

    /**
     * Declare the JOIN constraint ON.
     *
     * @param   string  $expression    Expression.
     * @return  \Hoa\Database\Query\Select
     */
    public function on($expression)
    {
        $this->_from[key($this->_from)] =
            current($this->_from) .
            ' ON ' . $expression;

        return $this->_parent;
    }

    /**
     * Declare the JOIN constraint USING.
     *
     * @param   string  $expression    Expression.
     * @param   ...     ...
     * @return  \Hoa\Database\Query\Select
     */
    public function using($expression)
    {
        $this->_from[key($this->_from)] =
            current($this->_from) .
            ' USING (' .
            implode(', ', func_get_args()) . ')';

        return $this->_parent;
    }
}
