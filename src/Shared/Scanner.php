<?php

namespace HansOtt\GraphQL\Shared;

interface Scanner
{
    /**
     * @throws ScannerReachedEnd
     *
     * @return mixed
     */
    public function peek();

    /**
     * @throws ScannerReachedEnd
     *
     * @return mixed
     */
    public function next();

    /**
     * @return bool
     */
    public function eof();

    /**
     * @throws ScannerReachedEnd
     *
     * @return mixed
     */
    public function back();
}
