<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class PHPExcelAction extends UserAction {

    public function index()
    {
        p(Excel::excel2Arr('test.xlsx'));
    }
}
