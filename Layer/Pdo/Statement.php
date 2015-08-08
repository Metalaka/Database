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

namespace Hoa\Database\Layer\Pdo;

use Hoa\Database;

/**
 * Class \Hoa\Database\Layer\Pdo\Statement.
 *
 * Wrap PDOStatement.
 *
 * @copyright  Copyright © 2007-2016 Hoa community
 * @license    New BSD License
 */
class Statement implements Database\IDal\WrapperStatement
{
    /**
     * The statement instance.
     *
     * @var \PDOStatement
     */
    protected $_statement   = null;

    /**
     * The cursor orientation.
     *
     * @var int
     */
    protected $_orientation = Database\DalStatement::FORWARD;

    /**
     * The start cursor offset.
     *
     * @var int
     */
    protected $_offset      = Database\DalStatement::FROM_START;

    /**
     * The fetching style.
     *
     * @var int
     */
    protected $_style       = Database\DalStatement::AS_MAP;



    /**
     * Create a statement instance.
     *
     * @param   \PDOStatement  $statement    The PDOStatement instance.
     * @return  void
     */
    public function __construct(\PDOStatement $statement)
    {
        $this->setStatement($statement);

        return;
    }

    /**
     * Set the statement instance.
     *
     * @param   \PDOStatement  $statement    The PDOStatement instance.
     * @return  \PDOStatement
     */
    protected function setStatement(\PDOStatement $statement)
    {
        $old              = $this->_statement;
        $this->_statement = $statement;

        return $old;
    }

    /**
     * Get the statement instance.
     *
     * @return  \PDOStatement
     */
    protected function getStatement()
    {
        return $this->_statement;
    }

    /**
     * Execute a prepared statement.
     *
     * @param   array   $bindParameters    Bind parameters values if bindParam
     *                                     is not called.
     * @return  \Hoa\Database\Layer\Pdo\Statement
     * @throws  \Hoa\Database\Exception
     */
    public function execute(array $bindParameters = null)
    {
        if (false === $this->getStatement()->execute($bindParameters)) {
            throw new Database\Exception(
                '%3$s (%1$s/%2$d)',
                0,
                $this->errorInfo()
            );
        }

        return $this;
    }

    /**
     * Bind a parameter to te specified variable name.
     *
     * @param   mixed   $parameter    Parameter name.
     * @param   mixed   $value        Parameter value.
     * @param   int     $type         Type of value.
     * @param   int     $length       Length of data type.
     * @return  bool
     * @throws  \Hoa\Database\Exception
     */
    public function bindParameter(
        $parameter,
        &$value,
        $type = null,
        $length = null
    ) {
        if (null === $type) {
            return $this->getStatement()->bindParam($parameter, $value);
        }

        if (null === $length) {
            return $this->getStatement()->bindParam($parameter, $value, $type);
        }

        return $this->getStatement()->bindParam($parameter, $value, $type, $length);
    }

    /**
     * Return an array containing all of the result set rows.
     *
     * @return  array
     * @throws  \Hoa\Database\Exception
     */
    public function fetchAll()
    {
        return $this->getStatement()->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Set the Iterator fetching style.
     *
     * @param   int        $orientation    This value must be
     *                                     DalStatement::FORWARD or
     *                                     DalStatement::BACKWARD constant.
     * @param   int        $offset         This value must be one of the
     *                                     DalStatement::FROM_* constants or
     *                                     an arbitrary offset.
     * @param   int|array  $style          This value must be one of the
     *                                     DalStatement::AS_* constants.
     * @return  \Hoa\Database\Layer\Pdo\Statement
     */
    public function setFetchingStyle(
        $orientation = Database\DalStatement::FORWARD,
        $offset      = Database\DalStatement::FROM_START,
        $style       = Database\DalStatement::AS_MAP
    ) {
        $this->_orientation = $orientation;
        $this->_offset      = $offset;
        $this->_style       = $style;

        return $this;
    }

    /**
     * Get an Iterator.
     *
     * @return  \Hoa\Database\Layer\Pdo\Iterator
     */
    public function getIterator()
    {
        return new Iterator(
            $this->getStatement(),
            $this->_orientation,
            $this->_offset,
            $this->_style
        );
    }

    /**
     * Fetch the first row in the result set.
     *
     * @param   int  $style    Must be one of the DalStatement::AS_* constants.
     * @return  mixed
     */
    public function fetchFirst($style = null)
    {
        return $this->fetch($style, \PDO::FETCH_ORI_FIRST);
    }

    /**
     * Fetch the last row in the result set.
     *
     * @param   int  $style    Must be one of the DalStatement::AS_* constants.
     * @return  mixed
     */
    public function fetchLast($style = null)
    {
        return $this->fetch($style, \PDO::FETCH_ORI_LAST);
    }

    /**
     * Fetch a row in the result set.
     *
     * @param   int  $style          Must be one of the DalStatement::AS_*
     *                               constants.
     * @param   int  $orientation    Must be one of the \PDO::FETCH_ORI_*
     *                               constants.
     * @return  mixed
     */
    protected function fetch($style, $orientation)
    {
        return $this->getStatement()->fetch(
            $style ?: $this->_style,
            $orientation
        );
    }

    /**
     * Return a single column from the next row of the result set or false if
     * there is no more row.
     *
     * @param   int  $column    Column index.
     * @return  mixed
     * @throws  \Hoa\Database\Exception
     */
    public function fetchColumn($column = 0)
    {
        return $this->getStatement()->fetchColumn($column);
    }

    /**
     * Close the cursor, enabling the statement to be executed again.
     *
     * @return  bool
     * @throws  \Hoa\Database\Exception
     */
    public function closeCursor()
    {
        return $this->getStatement()->closeCursor();
    }

    /**
     * Fetch the SQLSTATE associated with the last operation on the statement
     * handle.
     *
     * @return  string
     * @throws  \Hoa\Database\Exception
     */
    public function errorCode()
    {
        return $this->getStatement()->errorCode();
    }

    /**
     * Fetch extends error information associated with the last operation on the
     * statement handle.
     *
     * @return  array
     * @throws  \Hoa\Database\Exception
     */
    public function errorInfo()
    {
        return $this->getStatement()->errorInfo();
    }
}
