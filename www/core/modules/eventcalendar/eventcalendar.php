<?php

    Application::loadObjectClass('eventcalendar/events_calendar');
    Application::loadObjectClass('eventcalendar/calendar');

    define('AUTO_REVIEW_EVENTS', 1);

    class EventcalendarModule extends Module {

        public function run($params=array()) {

            $smarty = Application::getSmarty();
            $breadcrumbs = Application::getBreadcrumbs();
            $breadcrumbs->addNode('/eventcalendar', 'Event Calendar');

            $action = 'list';
            $object = new events_calendar();
            if(Request::get('done')){
                $action = 'create_ok';
                $breadcrumbs->addNode('/eventcalendar?done=create', 'Event Created');
                Application::loadObjectClass('blocks');
                $block = new blocks();
                $block = $block->load(5);
            }

            if((int)Request::get('id')){
                $action = 'view_event';

                $object = $object->loadToSite((int)Request::get('id'));
                if (!$object) display_404_page();
                $smarty->assign('event_item', $object);
                $popup_window = 1;
            }

            if(Request::get('create') || Request::isPostMethod()){
                $action = 'create';
                $breadcrumbs->addNode('/eventcalendar?create=new', 'Add an Event');
                Application::loadLibrary( 'olmi/form' );
                $form = new BaseForm();
                $form = $object->form_to_site($form);

                $page = Application::getPage();
                $page->addScript('/js/lib/jquery/jquery.js');
                $page->addScript('/js/lib/jquery/jquery_ui.js');
                $page->addStylesheet('/css/jquery_ui.css');


                if (Request::isPostMethod()) {
                    $form->LoadFromRequest($_REQUEST);
                    $form->UpdateObject($object);
                    $object->trim_fields();
                    $errors = $object->validate();
                    if(!CheckValidateEmail($form->getValue('email'))){
                        $errors[] = "E-mail address format is invalid!";
                    }
                    if (sizeof($errors) == 0) {
                        $object->date_posted = date("Y-m-d");
                        if(AUTO_REVIEW_EVENTS){
                            $object->is_active = 1;
                            $object->date_reviewed = date("Y-m-d");
                        }
                        /*$object->time_begin = $form->getValue('time_begin_h').':'.$form->getValue('time_begin_i');
                        $object->time_end = $form->getValue('time_end_h').':'.$form->getValue('time_end_i');*/
                        $object->time_begin = "00:00";
                        $object->time_end = "00:00";

                        $dbEngine = Application::getDbEngine();
                        $dbEngine->insertObject($object, $object->get_table_name());

                        Application::loadObjectClass('email_list');
                        $email_list = new email_list();
                        $email_list = $email_list->getEmailFromForm('eventcalendar_add');
                        mailto('create_new_event', $email_list,
                                        array('name' => $object->name,
                                              'text' => $object->comments,
                                              'email' => $object->email,
                                        ));

                        Redirector::redirect(Application::getSeoUrl('/eventcalendar') . "?done=".$action);
                    }else{
                        $smarty->assign("errors", implode("<br>",$errors));
                    }
                }

                $smarty->assign("form", $form);
            }

            if($action == 'list'){
                $event_type = intval(@$_REQUEST['event_type']);
                $region = intval(@$_REQUEST['region']);
                $form_events_type = Application::getFilter('eventcalendar', array('mode' => 'front'));
                $form_events_type->LoadFromRequest($_REQUEST);

                $form_events_type->setValue('event_type', $event_type);
                $form_events_type->setValue('region', $region);

                $smarty->assign("form_events_type", $form_events_type);
               // $smarty->assign("event_type", $event_type);

                if(Request::get('month') && Request::get('year')){
                    $month = addslashes(Request::get('month'));
                    $year = addslashes(Request::get('year'));
                }else{
                    $month = date("m");
                    $year = date("Y");
                }

                $smarty->assign("month", $month);
                $smarty->assign("year",  $year);

                $eventcalendar = new EventCalendarToSite();
                $eventcalendar->event_type = $event_type;
                $eventcalendar->region = $region;
                $event_list = $eventcalendar->render($month, $year, date("Y-m-d"));
                $smarty->assign("event_list", $event_list);
            }


            $smarty->assign("action", $action);
            if ($action == 'create_ok')
            {
                $smarty->assign("return_block", $block);
            }

            Application::loadObjectClass('blocks');
            $block = new blocks();
            $smarty->assign("page_bottom_block", $block->getBlockToSite('event_calendar_page_bottom'));
            $smarty->assign("module_url", Application::getSeoUrl('/eventcalendar'));

            $breadcrumbs_html = Application::getBlockContent('breadcrumbs');
            $smarty->assign("breadcrumbs", $breadcrumbs_html);

            $template_path = $this->getTemplatePath();
            return $smarty->fetch($template_path);


        }

    }
