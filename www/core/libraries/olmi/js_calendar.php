<?php


	class TJSCalendarField extends TEditField {
		
		protected $widget_options;
		
		function __construct($aName, $aValue = "", $widget_options=array()){
			parent::TEditField($aName, $aValue);
			$this->Size = 10;
			$this->widget_options = $widget_options;
		}
		
		function GetAsHTML($aSize = 0, $aMaxLength = 0){
			$this->attributes .= 'class="datepicker"';
			
			$Res = parent::GetAsHTML($aSize, $aMaxLength);
			
			$page = Application::getPage();
			$page->addScript(Application::getApplicationUrl() . "/static/js/jquery.js");
			$page->addScript(Application::getApplicationUrl() . "/static/js/jquery-ui/jquery-ui.min.js");
			$page->addStylesheet(Application::getApplicationUrl() . "/static/js/jquery-ui/css/ui-lightness/jquery-ui.css");
			
			$year_start = date("Y") - 5;
			$year_end = date("Y") + 5;
			
			
			$options = array(
				'dayNamesMin' => array('Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'),
				'monthNames' => array('Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'),
				'monthNamesShort' => array('Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн', 'Июл', 'Авг', 'Сент', 'Окт', 'Ноя', 'Дек'),			
				'firstDay' => 1,
				'dateFormat' => 'dd.mm.yy',
				'changeMonth' => true,
				'changeYear' => true,
				'yearRange' => "$year_start:$year_end"
			);
			
			foreach ($this->widget_options as $k=>$v) {
				$options[$k] = $v;
			}
			
			$options = json_encode($options);
			
			$Res .= "
				<script type=\"text/javascript\">
					jQuery('input[name=$this->Name].datepicker').datepicker($options);
				</script>	
			";
			
			return $Res;
		}
		
		
		
	}