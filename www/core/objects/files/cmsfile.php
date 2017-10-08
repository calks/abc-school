<?php

    class cmsfile extends CMSObject {
        var $id;
        var $fname;
        var $dir_name;

        function load_list($base, $prefix='') {
            $d = dir($base);
            $res = array();
            $i = 0;
            while (false !== ($entry = $d->read())) {
                if (checkFilename($entry)){
                    if(is_dir($base.$entry)){
                        $res[$i]['base'] = $base;
                        $res[$i]['dir'] = $entry;
                        $childs = $this->load_list($base.$entry.'/',$prefix.$entry.'/');
                        $res[$i]['sizelist'] = sizeof($childs);
                        $res[$i]['list'] = $childs;

                        if(preg_match("/^\d+$/", $entry)){
                            $doc = new document();
                            $doc = $doc->load($entry);
                            if(@$doc->menu_title){
                               $res[$i]['comment'] = '('.$doc->menu_title.')';
                            }
                        }else{
                            $res[$i]['comment'] = '';
                        }

                    }
                    else{
                        $res[$i]['files']['name'] = $entry;
                        $res[$i]['files']['date'] = filemtime($base.$entry);
                        $res[$i]['files']['size'] = PrintFileSize(filesize($base.$entry));
                        $res[$i]['files']['icon'] = getIcon($entry);
                        $res[$i]['files']['parent'] = preg_replace("/^(.+?)([^\/]+\/)$/", '\\2', $base);

                    }
                    $i++;
                }
            }
            $d->close();
            usort($res, "sort_files");
            return($res);
        }

        function make_form(&$form) {
            $form->addField(new THiddenField("id"));
            $form->addField(new TFileField("fname", 60, ''));
            $form->addField(new TSelectField('dir_name', '', getDirName(FILE_PATH)));
            return $form;
        }

        function make_form_multiply(&$form) {
            $form->addField(new THiddenField("id"));
            for ($i = 1; $i <= 10; $i++)
            {
                $form->addField(new TFileField("fname_{$i}", 60, ''));
            }

            $form->addField(new TSelectField('dir_name', '', getDirName(FILE_PATH)));
            return $form;
        }

        function make_form_dir(&$form) {
            $form->addField(new THiddenField("id"));
            $form->addField(new TEditField("fname"));
            $form->addField(new TSelectField('dir_name', '', getDirName(FILE_PATH)));
            return $form;
        }
    }

    function PrintFileSize($size){
        $mb = 1024 * 1024;
        $kb = 1024;
        if($size > $mb){
            $res = number_format($size/$mb, 2, '.', '').' Mb';
        }
        else{
            $res =number_format($size/$kb, 2, '.', '').' Kb';
        }
        return $res;
    }

    function sort_files($a,$b){
        $a0 = (isset($a["dir"])?1:0);
        $b0 = (isset($b["dir"])?1:0);

        $a1 = (isset($a["dir"])?$a["dir"]:0);
        $b1 = (isset($b["dir"])?$b["dir"]:0);

        $a2 = (isset($a["files"])?$a["files"]:0);
        $b2 = (isset($b["files"])?$b["files"]:0);

        if ($a0 != $b0) return ($a0 > $b0) ? -1: +1;
        if ($a1 != $b1) return ($a1 > $b1) ? +1: -1;
        if ($a2 != $b2) return ($a2 > $b2) ? +1: -1;
        return 0;
    }

    function getDirName($base, $prefix = "-"){
        $d = dir($base);
        $res = array();
        if($base == FILE_PATH){
            $res[$base] = ' ..';
        }
        while (false !== ($entry = $d->read())) {
            if (checkFilename($entry)){
                if(is_dir($base.$entry)){
                    $res[$base.$entry.'/'] = $prefix.$entry;
                    $res = array_merge($res, getDirName($base.$entry.'/',$prefix.'-'));
                }
            }
        }
        $d->close();
        return($res);
    }

    function dirDelete($dir_name){
        $d = dir($dir_name);
        while (false !== ($entry = $d->read())) {
            if (checkFilename($entry)){
                if(is_dir($dir_name.$entry)){
                    dirDelete($dir_name.$entry.'/');
                }
                else{
                    @unlink($dir_name.$entry);
                }
            }
        }
        $d->close();
        rmdir($dir_name);
    }

    function checkFilename($filename){
        if ($filename != '.' && $filename != '..' && $filename !='CVS' && $filename != '.cvsignore' && $filename != '.htaccess'){
            return true;
        }
        return false;
    }

    function getIcon($filename){
        if(preg_match("/\.pdf$/i", $filename)) return 'icon_pdf';
        if(preg_match("/\.doc$/i", $filename)) return 'icon_word';
        if(preg_match("/\.gif$/i", $filename)) return 'icon_gif';
        if(preg_match("/\.(jpg|jpeg)$/i", $filename)) return 'icon_jpg';
        if(preg_match("/\.xls$/i", $filename)) return 'icon_xcel';
        if(preg_match("/\.(flw|swf)$/i", $filename)) return 'icon_flash';
        return 'icon_general';
    }

    function countToDir($base) {
        $i = 0;
        if (is_dir($base)){
            $d = dir($base);
            while (false !== ($entry = $d->read())) {
                if (checkFilename($entry)){
                    if(!is_dir($base.$entry)){
                        $i++;
                    }
                }
            }
            $d->close();
        }
        return($i);
    }
