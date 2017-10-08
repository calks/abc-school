<?php

    Application::loadLibrary('core/dataobject');

    class photo extends DataObject {
        const TABLE_NAME = 'photo';

        const IMAGE_TYPE_OTHER = 0;
        const IMAGE_TYPE_MEDIUM = 1;
        const IMAGE_TYPE_BIG = 2;
        const IMAGE_TYPE_LARGE = 3;
        const IMAGE_TYPE_SUPER = 4;

        var $id;
        var $filename;
        var $comments;
        var $iidobject;
        var $tablename;
        var $sortorder;

        function get_table_name() {
            return self::TABLE_NAME;
        }

        function getPrimaryKeyField() {
            return "id";
        }

        function mandatory_fields() {
            return array("filename");
        }

        /*
         function order_by() {
         return " order by sortorder asc";
         }
         */

        function getImageSize($type = self::IMAGE_TYPE_SUPER) {
            $dir = '';
            switch ($type) {
            case (self::IMAGE_TYPE_MEDIUM):
                {
                    $dir = 'medium';
                    break;
                }
            case (self::IMAGE_TYPE_BIG):
                {
                    $dir = 'big';
                    break;

                }
            case (self::IMAGE_TYPE_LARGE):
                {
                    $dir = 'large';
                    break;

                }
            case (self::IMAGE_TYPE_SUPER):
                {
                    $dir = 'super';
                    break;
                }
            }

            $filename = UPLOAD_PHOTOS."{$this->tablename}/{$this->iidobject}/{$dir}/{$this->filename}";

            if (is_file($filename)) list($w, $h) = getimagesize($filename);
            else $w = $h = 0;

            return array($w, $h);
        }

        function get_count_records($iidobject, $tablename) {
            $db = Application::getDb();
            $dbEngine = Application::getDbEngine();

            $table = $this->get_table_name();

            $query = "select count(*) from ".$table." where iidobject=".$dbEngine->prepareValue($iidobject)." and tablename=".$dbEngine->prepareValue($tablename);
            return $db->executeScalar($query);
        }

        function load_list($params) {
            $list = parent::load_list($params);
            if ('admin' != @$params['mode']) {
                $this->add_image_alts($list);
            }
            return $list;
        }

        protected function add_image_alts(&$photos_list) {

            $ids = array();
            $object_mapping = array();
            foreach ($photos_list as $key => $item) {
                $object_type = $item->tablename;
                $object_id = (int) ($item->iidobject );
                $ids[$object_type][$object_id] = $object_id;
                $object_mapping[$object_type][$object_id][] = $key;
            }


            if (!$ids) {
                return;
            }


            $db = Application::getDb();
            foreach ($ids as $object_class => $ids_item) {
                if (!is_subclass_of($object_class, 'DataObject')) continue;

                $ids_item = implode(',', $ids_item);

                $object = Application::getObjectInstance($object_class);
                $table_abr = $this->get_table_abr($object->get_table_name());
                $params['where'][] = "$table_abr.`id` IN( $ids_item )";
                $objects = $object->load_list($params);

                foreach ($objects as $object) {
                    foreach ($object_mapping[$object_class][$object->id ] as $key) {
                        $comment = $photos_list[$key]->comments;
                        $alt = $this->get_image_alt($object_type, $object->id, $comment, $object);
                        $photos_list[$key]->alt = $alt;
                    }
                }
            }

        }

        function get_image_alt($object_table, $object_id, $comment, $object = null) {

            if (!$object) {
                if (!$object_id) return '';
                if (!is_subclass_of($object_table, 'DataObject')) return $comment;
                //if (!in_array($object_table, array('home', 'business', 'realestate', 'point'))) return $comment;
                $object = Application::getObjectInstance($object_table);
                $object = $object->load($object_id);
            }

            if (!$object) return '';
            $attributes = $object->getPhotoAttributes();

            $alt = isset($attributes['alt']) ? $attributes['alt'] : '';

            if ($comment) $alt .= " - $comment";
            $alt = strip_tags($alt);
            $alt = htmlspecialchars($alt, ENT_QUOTES);

            return $alt;
        }

        function make_form(&$form) {
            global $iidobject, $tablename;
            $form->addField(new THiddenField('id'));
            $form->addField(new THiddenField('iidobject', $iidobject));
            $form->addField(new THiddenField('tablename', $tablename));
            $form->addField(new TFileField('filename', 80));
            $form->addField(new TEditField('comments', '', 80));
            $form->addField(new TEditField('sortorder', 100, 10, 10));
            $form->addField(new TCheckboxField("add_watermark", "1", false));
            return $form;
        }

        function get_one_photo($tablename, $iidobject, $level = "", $id = 0) {
            $db = Application::getDb();
            $table = self::TABLE_NAME;
            if ($level) $level = "$level/";
            $sqlid = "";
            if ($id) $sqlid = " AND `id`={$id} ";
            $sql = "
                SELECT * FROM `{$table}`
                WHERE
                    `filename` != '' AND
                    `tablename` = '{$tablename}' AND
                    `iidobject` = {$iidobject} {$sqlid}
                ORDER BY `sortorder`
                LIMIT 1";
            $photo = $db->executeSelectObject($sql);

            if ($photo) {
                $basepath = UPLOAD_PHOTOS."{$tablename}/{$iidobject}";
                $baseurl = PHOTOS_URL."{$tablename}/{$iidobject}";
                $filepath = UPLOAD_PHOTOS."{$tablename}/{$iidobject}/{$level}{$photo->filename}";
                //print_r($filepath);
                if (is_file($filepath)) {

                    $photo->alt = $this->get_image_alt($tablename, $iidobject, $photo->comments);
                    list($wb, $hb, $type, $attr) = getimagesize("$basepath/{$level}{$photo->filename}" );
                    $photo->src = "<img width=\"{$wb}\" height=\"{$hb}\" src=\"{$baseurl}/{$level}{$photo->filename}\" border=\"0\" alt=\"{$photo->alt}\" title=\"{$photo->alt}\">";
                    $photo->href = "{$tablename}/{$iidobject}/super{$photo->filename}";
                    $photo->src_level = "{$baseurl}/{$level}{$photo->filename}";

                    $big = "{$basepath}/big/{$photo->filename}";
                    list($wb, $hb, $type, $attr) = getimagesize($big);
                    $photo->bigsrc = "return big_photo(".$photo->id.",".($wb).",".($hb).")";

                    $super = "{$basepath}/super/{$photo->filename}";
                    if (is_file($super)) {
                        list($wb, $hb, $type, $attr) = getimagesize($super);
                        $photo->superWidth = $wb;
                        $photo->superHeight = $hb;
                    }
                }
            }
            return $photo;
        }

        function get_some_photo($tablename, $iidobject, $level = "", $attributes = "") {
            $db = Application::getDb();
            $table = self::TABLE_NAME;
            if ($level) $level = "{$level}/";

            $params['where'] = array(
                'tablename' => $tablename,
                'iidobject' => $iidobject
            );
            $params['order_by'] = photo::order_by();

            $r = $this->load_list($params);

            $result = array();

            $basePath = UPLOAD_PHOTOS."{$tablename}/{$iidobject}";
            $baseUrl = PHOTOS_URL."{$tablename}/{$iidobject}";

            foreach ($r as $photo) {
                $filename = "{$basePath}/{$level}{$photo->filename}";
                if (!is_file($filename)) continue;

                list($wb, $hb, $type, $attr) = getimagesize($filename);

                $photo->src = "<img width=\"{$wb}\" height=\"{$hb}\" src=\"{$baseUrl}/{$level}{$photo->filename}\"  border=\"0\"
                        alt=\"".str_replace('"', '', htmlentities(strip_tags($photo->comments)))."\" ".$attributes.">";

                $super = "{$basePath}/super/{$photo->filename}";
                if (is_file($super)) {
                    list($wb, $hb, $type, $attr) = getimagesize($super);
                    $photo->superWidth = $wb;
                    $photo->superHeight = $hb;
                }
                $result[] = $photo;
            }

            return $result ? $result : null;
        }

        function get_photo_by_query($query) {
            $dbEngine = Application::getDbEngine();
            $table = $this->get_table_name();
            list($tablename, $iid, $format, $filename) = explode('/', $query, 4);
            $tablename = $dbEngine->prepareValue($tablename);
            $iid = intval($iid);
            $filename = $dbEngine->prepareValue($filename);
            $sql = "
            SELECT *
            FROM {$table} AS p
            WHERE
                 p.`tablename` = {$tablename} AND
                 p.`iidobject` = {$iid} AND
                 p.`filename` = {$filename}
            LIMIT 1";

            $object = $dbEngine->LoadObject($sql, get_class($this));
            if (!$object) return null;
            return $object;
        }

        function get_next_photo() {
            $dbEngine = Application::getDbEngine();
            $table = $this->get_table_name();
            $tablename = $dbEngine->prepareValue($this->tablename);
            $iid = intval($this->iidobject);
            $sortorder = intval($this->sortorder);
            $photoId = intval($this->id);

            $object = $dbEngine->LoadObject("
            SELECT *
            FROM `{$table}` AS p
            WHERE
                 p.`tablename` = {$tablename} AND
                 p.`iidobject` = {$iid} AND
                 p.`sortorder` > {$sortorder}
            ORDER BY
                p.`sortorder` ASC
            LIMIT 1
            ", get_class($this));
            if (is_object($object)) {
                return $object;
            } else {
                $object = $dbEngine->LoadObject("SELECT *
                 FROM {$table} AS p
                 WHERE
                     p.`tablename` = {$tablename} AND
                     p.`iidobject` = {$iid} AND
                     p.`sortorder` IN (
                         SELECT MIN(`sortorder`)
                         FROM photo
                         WHERE
                             `tablename` = {$tablename} AND
                             `iidobject` = {$iid})
                 LIMIT 1", get_class($this));

                if (is_object($object)) {
                    return $object;
                } else {
                    return null;
                }
            }

        }

        function get_prev_photo() {
            $dbEngine = Application::getDbEngine();
            $table = $this->get_table_name();
            $tablename = $dbEngine->prepareValue($this->tablename);
            $iid = intval($this->iidobject);
            $sortorder = intval($this->sortorder);

            $photoId = intval($this->id);

            $object = $dbEngine->LoadObject("
            SELECT *
            FROM `{$table}` AS p
            WHERE
                 p.`tablename` = {$tablename} AND
                 p.`iidobject` = {$iid} AND
                 p.`sortorder` < {$sortorder}
            ORDER BY
                p.`sortorder` DESC
            LIMIT 1
            ", get_class($this));

            if (is_object($object)) {
                return $object;
            } else {
                $object = $dbEngine->LoadObject("SELECT *
                 FROM {$table} AS p
                 WHERE
                     p.`tablename` = {$tablename} AND
                     p.`iidobject` = {$iid} AND
                     p.`sortorder` IN (
                         SELECT MAX(`sortorder`)
                         FROM photo
                         WHERE
                             `tablename` = {$tablename} AND
                             `iidobject` = {$iid})
                 LIMIT 1", get_class($this));

                if (is_object($object)) {
                    return $object;
                } else {
                    return null;
                }
            }
        }

        function delete_file() {
            $dbEngine = Application::getDbEngine();

            if (is_file(UPLOAD_PHOTOS.$this->tablename."/".$this->iidobject."/big/".$this->filename)) {
                unlink(UPLOAD_PHOTOS.$this->tablename."/".$this->iidobject."/big/".$this->filename);
            }

            if (is_file(UPLOAD_PHOTOS.$this->tablename."/".$this->iidobject."/super/".$this->filename)) {
                unlink(UPLOAD_PHOTOS.$this->tablename."/".$this->iidobject."/super/".$this->filename);
            }

            if (is_file(UPLOAD_PHOTOS.$this->tablename."/".$this->iidobject."/large/".$this->filename)) {
                unlink(UPLOAD_PHOTOS.$this->tablename."/".$this->iidobject."/large/".$this->filename);
            }

            if (is_file(UPLOAD_PHOTOS.$this->tablename."/".$this->iidobject."/medium/".$this->filename)) {
                unlink(UPLOAD_PHOTOS.$this->tablename."/".$this->iidobject."/medium/".$this->filename);
            }
            if (is_file(UPLOAD_PHOTOS.$this->tablename."/".$this->iidobject."/".$this->filename)) {
                unlink(UPLOAD_PHOTOS.$this->tablename."/".$this->iidobject."/".$this->filename);
            }
            if (is_file(UPLOAD_PHOTOS.$this->tablename."/".$this->iidobject."/photos.xml")) {
                unlink(UPLOAD_PHOTOS.$this->tablename."/".$this->iidobject."/photos.xml");
            }
            $this->filename = "";
            $table = $this->get_table_name();
            $dbEngine->updateObject($this, $table);
        }

        function delete() {
            $dbEngine = Application::getDbEngine();
            $table = $this->get_table_name();
            $this->delete_file();
            $dbEngine->deleteObject($this, $table);
        }

        function deleteAllPhotos() {
            $db = Application::getDb();

            $delete = "
            DELETE FROM ".photo::get_table_name()."
            WHERE
                `iidobject` = ".intval($this->iidobject)." AND
                `tablename` = ".intval($this->tablename);

            $db->execute($delete);

            $path = UPLOAD_PHOTOS."{$this->tablename}/{$this->iidobject}";

            $this->_delPattern("{$path}/*");
            $this->_delPattern("{$path}/medium/*");
            $this->_delPattern("{$path}/super/*");
            $this->_delPattern("{$path}/big/*");
            $this->_delPattern("{$path}/large/*");

        }

        function _delPattern($pattern) {
            $files = glob($pattern);
            if (is_array($files)) {
                foreach ($files as $file) {
                    if (is_file($file)) {
                        unlink($file);
                    }
                }
            }
        }

        function delete_to_object($tablename, $iidobject) {
            $db = Application::getDb();
            $dbEngine = Application::getDbEngine();
            $table = $this->get_table_name();

            $query = "select * from ".$table." where tablename = ".$dbEngine->prepareValue($tablename)." and iidobject = ".$dbEngine->prepareValue($iidobject);
            $res = $dbEngine->LoadQueryResults($query, get_class($this));
            foreach ($res as $object) {
                $object->delete();
            }
            if (is_dir(UPLOAD_PHOTOS.$tablename."/".$iidobject)) {
                @rmdir(UPLOAD_PHOTOS.$tablename."/".$iidobject."/big");
                @rmdir(UPLOAD_PHOTOS.$tablename."/".$iidobject."/medium");
                @rmdir(UPLOAD_PHOTOS.$tablename."/".$iidobject."/super");
                @rmdir(UPLOAD_PHOTOS.$tablename."/".$iidobject."/large");
                @rmdir(UPLOAD_PHOTOS.$tablename."/".$iidobject);
            }
        }

        function normalizeSortOrder() {
            global $dbEngine, $db;
            $table = $this->get_table_name();
            $query = "
                SELECT *
                FROM {$table}
                WHERE
                    `iidobject` = ".$dbEngine->prepareValue($this->iidobject)." AND
                    `tablename` = ".$dbEngine->prepareValue($this->tablename)."
                ORDER BY `sortorder` ASC, `id` ASC
                    ";
            $photos = $dbEngine->LoadQueryResults($query, get_class($this));

            $this_ = $this->load($this->id, array('mode' => 'admin'));

            $this->sortorder = $this->sortorder >= 1 ? $this->sortorder : 1;
            $this->sortorder = $this->sortorder > count($photos) ? count($photos) : $this->sortorder;

            $i = 1;
            foreach ($photos as $photo) {
                if ($i == $this->sortorder) {
                    $this_->sortorder = $i++;
                    $dbEngine->updateObject($this_, $table);
                }

                if ($photo->id != $this_->id) {
                    $photo->sortorder = $i++;
                    $dbEngine->updateObject($photo, $table);
                }

            }
        }

        function createXML($tablename, $iidobject) {
            die('photo::createXML is deprecated');
        }

        function add_count_into_object_list(&$list) {
            $ids = array();
            $object_mapping = array();

            foreach ($list as $key => $item) {
                $ids[$item->getName()][] = $item->id;
                $object_mapping[$item->getName()][$item->id] = $key;
            }

            $conditions = array();
            foreach ($ids as $object_type => $object_ids) {
                if (!$object_ids) continue;
                foreach ($object_ids as & $id) $id = (int) $id;
                $object_ids = implode(',', $object_ids);
                $object_type = addslashes($object_type);
                $conditions[] = "tablename='$object_type' AND iidobject IN($object_ids)";
            }

            if (!$conditions) return;

            $db = Application::getDb();
            $where = ' WHERE ' . implode(' OR ', $conditions);
            $table = $this->get_table_name();
            $sql = "
                SELECT tablename, iidobject, COUNT(*) as photos_count
                FROM $table $where
                GROUP BY iidobject, tablename
            ";
            $count_list = $db->executeSelectAllObjects($sql);

            foreach ($count_list as $c) {
                $item_key = $object_mapping[$c->tablename][$c->iidobject];
                $list[$item_key]->photos_count = $c->photos_count;
            }

        }


        // this function should be preferred to add images for object list
        // cause it is the fastest one (generally because it don't load objects
        // to retrieve image-related data)
        function add_images_into_object_list(&$list) {
            if (USE_PROFILER) {
                $profiler = new profiler("photo::add_images_into_object_list()");
                $profiler->start();
            }

            $ids = array();
            $object_mapping = array();

            foreach ($list as $key => $item) {
                $ids[$item->getName()][] = $item->id;
                $object_mapping[$item->getName()][$item->id] = $key;
                $item->photos = array();
                $item->photo = null;
            }

            $conditions = array();
            foreach ($ids as $object_type => $object_ids) {
                if (!$object_ids) continue;
                foreach ($object_ids as & $id) $id = (int) $id;
                $object_ids = implode(',', $object_ids);
                $object_type = addslashes($object_type);
                $conditions[] = "tablename='$object_type' AND iidobject IN($object_ids)";
            }

            if (!$conditions) {
                if (USE_PROFILER) $profiler->stop();
                return;
            }

            $params = array('mode' => '');
            $params['where'] = implode(' OR ', $conditions);

            // because we need ONLY data (no alts, no sizes no object-related
            // info which is retrieved by photo::load_list())
            $photos_list = parent::load_list($params);

            foreach ($photos_list as $p) {
                $item_key = $object_mapping[$p->tablename][$p->iidobject];
                $p->alt = $this->get_image_alt($p->tablename, $p->iidobject, $p->comments, $list[$item_key]);
                $p->src = $this->getHtmlTag($p, 'small');
                $p->src_big = $this->getHtmlTag($p, 'big');
                if (!$list[$item_key]->photo) $list[$item_key]->photo = $p;
                $list[$item_key]->photos[] = $p;
            }

            if (USE_PROFILER) $profiler->stop();
        }

        protected function getHtmlTag($photo_obj, $photo_size='') {
            $base_url = PHOTOS_URL;
            if (substr($base_url, strlen($base_url)-1) != '/') $base_url .= '/';
            $src = PHOTOS_URL . "$photo_obj->tablename/$photo_obj->iidobject";
            if ($photo_size && $photo_size!='small') $src .= "/$photo_size";
            $src .= "/$photo_obj->filename";

            return "<img src=\"$src\" alt=\"$photo_obj->alt\" title=\"$photo_obj->alt\">";
        }

    }

