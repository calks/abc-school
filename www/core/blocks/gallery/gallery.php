<?php

    Application::loadObjectClass('photo');

    class GalleryBlock extends Block {
        public function run($params = array()) {
            $object_name = @$params['object_name'];
            $object_id = @$params['object_id'];
            $gallery_width = @$params['gallery_width'];

            $aliasP = DataObject::getTableAlias(photo::TABLE_NAME);

            $params_gallery = array();
            $params_gallery['where'][] = "{$aliasP}.`tablename` = '{$object_name}'";
            $params_gallery['where'][] = "{$aliasP}.`iidobject` = {$object_id}";
            $params_gallery['order_by'][] = "{$aliasP}.`sortorder`";

            if (!$object_name || !$object_id) {
                return '';
            }

            $photo = Application::getObjectInstance('photo');
            $photos = $photo->load_list($params_gallery);

            foreach ($photos as & $photo) {
                list($ws, $hs) = $photo->getImageSize(photo::IMAGE_TYPE_SUPER);
                list($wb, $hb) = $photo->getImageSize(photo::IMAGE_TYPE_BIG);

                if ($ws && $hs) {
                	if( $ws >= 430 ) continue;
                    $eps = (int) ($ws / 100);
                    $hformat = (int) ($ws * 0.75);
                    if ($hs < $hformat - $eps || $hs > $hformat + $eps) $photo->comments = '';
                } elseif ($wb && $hb) {
                	if( $wb >= 430 ) continue;
                	$eps = (int) ($wb / 100);
                    $hformat = (int) ($wb * 0.75);
                    if ($hb < $hformat - $eps || $hb > $hformat + $eps) $photo->comments = '';
                }
            }

            $smarty = Application::getSmarty();

            $smarty->assign('photos', $photos);
            $smarty->assign('panel_width', $gallery_width);
            $smarty->assign('object_name', $object_name);
            $smarty->assign('object_id', $object_id);
            $popup_url = Application::getSeoUrl("/$object_name/all_photos/$object_id");
            $smarty->assign('popup_url', $popup_url);

            $smarty->assign('object_id', $object_id);

            $template_path = $this->getTemplatePath();
            return trim($smarty->fetch($template_path));
        }
    }
