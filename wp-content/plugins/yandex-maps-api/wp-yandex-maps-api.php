<?php
/**
 * Plugin Name: Yandex Maps API
 * Plugin URI: http://xn--80aegccaes4apfcakpli6e.xn--p1ai/yandex-maps-api-for-wordpress/
 * Description: Insert <a href="http://tech.yandex.ru/maps">Yandex Maps with facilities API 2.1</a> into posts
 * Version: 1.1.4
 * Author: VasudevaServerRus
 * Author URI: http://dreambooks.ru/
 */

/* Created by VasudevaServerRus
   Copyright (C) 2014 VasudevaServerRus
   Inspired By Yandex Maps for WordPress Plugin                                                                          

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
*/

if (strpos(strtolower($_SERVER['SCRIPT_NAME']),strtolower(basename(__FILE__)))){
   header('HTTP/1.0 403 Forbidden');
   exit('Forbidden');
}

class wpYandexMapAPI{
   private static $hasMap;           // $hasMap==true if there are any map on the page
	private static $YaMapNumber = 0;  // map id for multiple maps per page
	private static $YaMapApiUrl;      // Full url to the yandex Maps JavaScript

   public function __construct(){
      $blog_lang = get_bloginfo('language','display');
		$this::$YaMapApiUrl = "http://api-maps.yandex.ru/2.1/?load=package.full&lang=".$blog_lang;
		wp_register_script('yandexMaps', $this::$YaMapApiUrl);
      //wp_enqueue_script('yandexMaps');              //uncomment to load script on all pages than to insert map in widget
      //add_filter('widget_text', 'do_shortcode');    //uncomment to insert shortcode in widget
	}

	//Check for map in posts
	public function scanYaMapInPost($posts){
		if (is_admin()){
			$this::$hasMap = false;
			return $posts;
		}
      if (is_array($posts)){
         foreach ($posts as $post){
            $pos = strpos($post->post_content,'[yandexMap');
            if ($pos !== false){
               $this::$hasMap = true;
               break;
            }
         }
      }else{
         $pos = strpos($posts,'[yandexMap');
         if ($pos !== false){
            $this::$hasMap = true;
         }
      }
		return $posts;
	}

	//Links JavaScript only if there are maps on page
	public function wpHead(){ 
      if ($this::$hasMap){
			wp_enqueue_script('yandexMaps');
		}
	}

   //debug function
   //$this->vasu_var_export($v);
	public function vasu_var_export($pS){ 
      $S = "\n\n". var_export($pS,TRUE);
      error_log($S,3,dirname(__FILE__) . "/debug.log");
	}

   //check value
	public function getTrueValue($value){
		if (strtolower($value) == 'true'){
			$vl = 'true';
		}else{
			$vl = 'false';
		}
		return $vl;
	}

	// Replaces false words
	public function fixFalseWord($value){
      $value = str_replace('(lt)','<',$value);
      $value = str_replace('(gt)','>',$value);
      $value = str_replace('(quot)','"',$value);
      $value = str_replace('(equiv)','=',$value);
      return $value;
	}

	// Replaces skobki
	public function fixSkobki($value){
      $value = str_replace('(','[',$value);
      $value = str_replace(')',']',$value);
      return $value;
	}

	//Main process
	public function handleShortcodes($attr, $content){
      $cr = "\n";
      $this::$YaMapNumber++;
      if (isset($attr['width']) && ctype_digit($attr['width'])){
			$attr['width'] .= 'px';
		}
		if (isset($attr['height']) && ctype_digit($attr['height'])){
			$attr['height'] .= 'px';
		}
      $mapProp = (object)shortcode_atts(array(
           'description' => '',
                'center' => '55.755768,37.617671',
                  'type' => 'map',
           'zoomcontrol' => 'true',
          'typeselector' => 'true',
             'scaleline' => 'false',
                'search' => 'false',
           'routeeditor' => 'false',
           'geolocation' => 'false',
            'fullscreen' => 'false',
               'traffic' => 'false',
            'showcoords' => 'false',
           'zoom_inital' => '',
              'zoom_min' => '0',
              'zoom_max' => '17',
                 'width' => '100%',
                'height' => '600px'), $attr);
      $YaMapDescription = "";
      $YaMapLabels = "";
      $YaMapRectangles = "";
      $YaMapGeo = "";
      $YaMapObj = "";
      $YaMapSetBounds = "";
      $YaMapRoute = "";
      $l_OnClickCoord = array();
      if ($mapProp->description!=''){
         $objbody = $mapProp->description;
         $objbody = $this->fixFalseWord($objbody);
         $YaMapDescription = "<div id='yamap_f_{$this::$YaMapNumber}' style='width:{$mapProp->width}; margin:7px;' class='yamapapi_f'>$objbody</div>";
      }
      $l = str_replace('<br />','',$content);
      $l = str_replace('&#8221;',"'",$l);
      $l = str_replace('&#8243;',"'",$l);
      $l = trim($l);
      $sp = explode('[',$l);
      $cnt_list=count($sp);
      for($v=0;$v<$cnt_list; $v++){
         $x = $sp[$v];
         $x = str_replace(']','',$x);
         if (substr($x,0,11)=='yamap_label'){
            $x = str_replace('yamap_label','',$x);
            $x = trim($x);
            $x_attr = shortcode_parse_atts($x);
            $labelInfo = (object)shortcode_atts(array(
                       'coord' => '55.755768,37.617671',
                 'description' => '',
                         'url' => '',
                  'header_txt' => '',
                  'header_url' => '',
                  'footer_txt' => '',
                  'footer_url' => '',
                    'icon_txt' => '',
                    'icon_url' => '',
                'routeonclick' => '',
                   'iconcolor' => '#3b5998',
                        'icon' => '',
                    'iconsize' => '16,16',
                  'iconoffset' => '-8,-8'), $x_attr);
            $lb = '';
            if (strlen($labelInfo->coord)>0){
               if ($labelInfo->icon==''){
                  $labelInfo->icon = 'http://api-maps.yandex.ru/i/0.4/icons/house.png';
               }
               switch ($labelInfo->icon){
               case 'icon':
               case 'dotIcon':
               case 'circleIcon':
               case 'circleDotIcon':
                  $lb  = ' var l_Placemark = new ymaps.Placemark(['.$labelInfo->coord.'],{},{preset:"islands#' . $labelInfo->icon . '",iconColor:"' . $labelInfo->iconcolor . '"});' . $cr;
                  break;
               default;
                  $lb  = ' var l_Placemark = new ymaps.Placemark(['.$labelInfo->coord.'],{},{iconLayout:"default#image", iconImageHref:"'.$labelInfo->icon.'",iconImageSize:['.$labelInfo->iconsize.'],iconImageOffset:['.$labelInfo->iconoffset.']});' . $cr;
                  break;
               }
               if ($labelInfo->description != ''){
                  $objbody = $labelInfo->description;
                  $objbody = $this->fixFalseWord($objbody);
                  if ($labelInfo->url != ''){
                     $objbody = '<a href="'.$labelInfo->url.'">'.$objbody.'</a>';
                  }
                  $lb .= " var x = '$objbody'; " . $cr . 'l_Placemark.properties.set("balloonContent",x);' . $cr;
               }
               if ($labelInfo->footer_txt != ''){
                  $objbody = $labelInfo->footer_txt;
                  $objbody = $this->fixFalseWord($objbody);
                  if ($labelInfo->footer_url != ''){
                     $objbody = '<a href="'.$labelInfo->footer_url.'">'.$objbody.'</a>';
                  }
                  $lb .= " var x = '$objbody'; " . 'l_Placemark.properties.set("balloonContentFooter",x);' . $cr;
               }
               if ($labelInfo->header_txt != ''){
                  $objbody = $labelInfo->header_txt;
                  $objbody = $this->fixFalseWord($objbody);
                  if ($labelInfo->header_url != ''){
                     $objbody = '<a href="'.$labelInfo->header_url.'">'.$objbody.'</a>';
                  }
                  $lb .= " var x = '$objbody';" . $cr;
                  $lb .= ' l_Placemark.properties.set("balloonContentHeader",x);' . $cr;
                  $lb .= ' l_Placemark.properties.set("hintContent",x);' . $cr;
               }
               if ($labelInfo->icon_txt != ''){
                  $objbody = $labelInfo->icon_txt;
                  $objbody = $this->fixFalseWord($objbody);
                  if ($labelInfo->icon_url != ''){
                     $objbody = '<a href="'.$labelInfo->icon_url.'">'.$objbody.'</a>';
                  }
                  $lb .= " var x = '$objbody';" . $cr;
                  $lb .= ' l_Placemark.properties.set("iconContent",x);' . $cr;
               }
               $lb .= ' l_collection.add(l_Placemark);' . $cr;
               if ($labelInfo->routeonclick == 'true'){
                  $l_OnClickCoord[] = '[' . $labelInfo->coord . ']';
               }
            }
            $YaMapLabels .= $lb;
         }elseif (substr($x,0,10)=='yamap_fill'){
            $x = str_replace('yamap_fill','',$x);
            $x = trim($x);
            $x_attr = shortcode_parse_atts($x);
            $rInfo = (object)shortcode_atts(array(
                      'area' => '',
               'description' => '',
                       'img' => '',
                   'opacity' => '0.7'), $x_attr);
            $l_area = $this->fixSkobki($rInfo->area);
            $lb   = ' var l_recTan = new ymaps.Rectangle(['.$l_area.'],{balloonContent:""},{fillImageHref:"'.$rInfo->img.'",fill:true,fillOpacity:'.$rInfo->opacity.', opacity:'.$rInfo->opacity.', fillMethod:"stretch", outline:false, interactivityModel:"default#transparent", strokeWidth:0});' . $cr;
            $lb  .= ' l_collection.add(l_recTan);' . $cr;
       		$YaMapRectangles .= $lb;
         }elseif (substr($x,0,9)=='yamap_geo'){
            $x = str_replace('yamap_geo','',$x);
            $x = trim($x);
            $x_attr = shortcode_parse_atts($x);
            $lb = ' ymaps.geoXml.load("'. $x_attr['url'] . '").then(function(res){l_YaMap' . $this::$YaMapNumber .'.geoObjects.add(res.geoObjects);});' . $cr;
       		$YaMapGeo .= $lb;
         }elseif (substr($x,0,11)=='yamap_obect'){
            $x = str_replace('yamap_obect','',$x);
            $x = trim($x);
            $x_attr = shortcode_parse_atts($x);
            $labelInfo = (object)shortcode_atts(array(
                        'type' => 'point',
                       'coord' => '55.755768,37.617671',
                      'radius' => '',
                       'width' => '2',
                       'color' => '',
               'color_opacity' => '0.7',
                        'fill' => '',
                'fill_opacity' => '0.7',
               'border_radius' => '0',
                 'description' => '',
                         'url' => '',
                  'header_txt' => '',
                  'header_url' => '',
                  'footer_txt' => '',
                  'footer_url' => '',
                        'icon' => '',
                    'iconsize' => '16,16',
                  'iconoffset' => '-8,-8'), $x_attr);
            $m_type = 'Point';
            $l_type = strtolower($labelInfo->type);
            if ($l_type=='point'){
               $m_type = 'Point';
            }elseif ($l_type=='line'){
               $m_type = 'LineString';
            }elseif ($l_type=='circle'){
               $m_type = 'Circle';
            }elseif ($l_type=='polygon'){
               $m_type = 'Polygon';
            }elseif ($l_type=='rectangle'){
               $m_type = 'Rectangle';
            }
            $x = $this->fixSkobki($labelInfo->coord);
            if ($labelInfo->radius==''){
               $x = '['.$x.']';
            }
            $lb  = ' var l_GeoObject = new ymaps.GeoObject(';
            $lb .= '{geometry:{type:"' . $m_type .'",coordinates:'.$x.'}';
            $lb .= ',properties:{}}';
            $lb .= ',{geodesic:true, draggable:false});' . $cr;
            if ($labelInfo->description!=''){
               $lb .= ' l_GeoObject.properties.set("balloonContent","' . $labelInfo->description .'");' . $cr;
               $lb .= ' l_GeoObject.properties.set("hintContent","' . $labelInfo->description .'");' . $cr;
            }
            $lb .= ' l_GeoObject.options.set("strokeWidth","' . $labelInfo->width .'");' . $cr;
            if ($labelInfo->color!=''){
               $lb .= ' l_GeoObject.options.set("strokeColor","' . $labelInfo->color .'");' . $cr;
               $lb .= ' l_GeoObject.options.set("strokeOpacity","' . $labelInfo->color_opacity .'");' . $cr;
            }
            if ($labelInfo->fill!=''){
               $lb .= ' l_GeoObject.options.set("fillColor","' . $labelInfo->fill .'");' . $cr;
               $lb .= ' l_GeoObject.options.set("fillOpacity","' . $labelInfo->fill_opacity .'");' . $cr;
            }
            if ($labelInfo->border_radius!=''){
               $lb .= ' l_GeoObject.options.set("borderRadius","' . $labelInfo->border_radius .'");' . $cr;
            }
            if ($labelInfo->radius!=''){
               $lb .= ' l_GeoObject.geometry.setRadius('.$labelInfo->radius.');' . $cr;
            }
            $lb .= ' l_collection.add(l_GeoObject);' . $cr;
            $YaMapObj .= $lb;
         }elseif (substr($x,0,11)=='yamap_route'){
            $x = str_replace('yamap_route','',$x);
            $x = trim($x);
            $x_attr = shortcode_parse_atts($x);
            $labelInfo = (object)shortcode_atts(array(
                       'start' => '(55.755768,37.617671)',
                        'stop' => '(55.752283,37.58351)',
                       'visit' => '',
                     'traffic' => 'false',
                        'icon' => '',
                    'iconsize' => '16,16',
                  'iconoffset' => '-8,-8'), $x_attr);
            $x_start = $this->fixSkobki($labelInfo->start);
            $x_stop = $this->fixSkobki($labelInfo->stop);
            $x_visit = '';
            if ($labelInfo->visit!=''){
               $x_visit = $this->fixSkobki($labelInfo->visit);
            }
            $lb  = ' ymaps.route([';
            $lb .= ' {type:"wayPoint", point:'.$x_start.'}';
            if ($x_visit!=''){
               $lb .= ",$x_visit";
            }
            $lb .= ',{type:"wayPoint", point:'.$x_stop.'}';
            $lb .= '],{mapStateAutoApply:false, avoidTrafficJams:'.$this->getTrueValue($labelInfo->traffic).'}).then(function(route){';
            $lb .= ' var points = route.getWayPoints();' . $cr;
            $lb .= ' points.options.set("fill", false);' . $cr;
            if ($labelInfo->icon!=''){
               $lb .= ' points.options.set("iconLayout", "default#image");' . $cr;
               $lb .= ' points.options.set("iconImageHref", "'.$labelInfo->icon.'");' . $cr;
            }
            $lb .= ' l_YaMap' . $this::$YaMapNumber . '.geoObjects.add(route);' . $cr;
            $lb .= '});' . $cr;
            $YaMapRoute .= $lb;
         }
      }
      $YaMapBuilder = "";
      $m_center = "center: [$mapProp->center],";
      $m_type = 'yandex#map';
      $l_type = strtolower($mapProp->type);
      if ($l_type=='map'){
         $m_type = 'yandex#map';
      }elseif ($l_type=='satellite'){
         $m_type = 'yandex#satellite';
      }elseif ($l_type=='hybrid'){
         $m_type = 'yandex#hybrid';
      }elseif ($l_type=='public'){
         $m_type = 'yandex#publicMap';
      }elseif ($l_type=='publichybrid'){
         $m_type = 'yandex#publicMapHybrid';
      }
      if ($mapProp->zoom_inital==''){
         $l_zoom = '10';
         $YaMapSetBounds = "if (l_collection.getLength()>0){l_YaMap{$this::$YaMapNumber}.setBounds(l_collection.getBounds());}";
      }else{
         $l_zoom = $mapProp->zoom_inital;
      }
      $YaMapBuilder .= ' var YMapId = document.getElementById("yamap_div_' . $this::$YaMapNumber . '");' . $cr;
      $YaMapBuilder .= ' var l_YMapId = YMapId.id;' . $cr;
      $YaMapBuilder .= ' var l_YaMap' . $this::$YaMapNumber . ' = new ymaps.Map(l_YMapId,{' . $m_center . " type:'$m_type', zoom:$l_zoom, controls:[]},{maxZoom:$mapProp->zoom_max, minZoom:$mapProp->zoom_min});" . $cr;
		$YaMapControls = "";
      if ($this->getTrueValue($mapProp->zoomcontrol)=='true'){
         $YaMapControls .= " l_YaMap" . $this::$YaMapNumber . ".controls.add('zoomControl', {left:'10px'});" . $cr;
      }
      if ($this->getTrueValue($mapProp->typeselector)=='true'){
         $YaMapControls .= " l_YaMap" . $this::$YaMapNumber . ".controls.add('typeSelector', {});" . $cr;
      }
      if ($this->getTrueValue($mapProp->scaleline)=='true'){
         $YaMapControls .= " l_YaMap" . $this::$YaMapNumber . ".controls.add('rulerControl');" . $cr;
      }
      if ($this->getTrueValue($mapProp->search)=='true'){
         $YaMapControls .= " l_YaMap" . $this::$YaMapNumber . ".controls.add('searchControl');" . $cr;
      }
      if ($this->getTrueValue($mapProp->routeeditor)=='true'){
         $YaMapControls .= " l_YaMap" . $this::$YaMapNumber . ".controls.add('routeEditor');" . $cr;
      }
      if ($this->getTrueValue($mapProp->geolocation)=='true'){
         $YaMapControls .= " l_YaMap" . $this::$YaMapNumber . ".controls.add('geolocationControl');" . $cr;
      }
      if ($this->getTrueValue($mapProp->fullscreen)=='true'){
         $YaMapControls .= " l_YaMap" . $this::$YaMapNumber . ".controls.add('fullscreenControl');" . $cr;
      }
      if ($this->getTrueValue($mapProp->traffic)=='true'){
         $YaMapControls .= " l_YaMap" . $this::$YaMapNumber . ".controls.add(new ymaps.control.TrafficControl({state: {providerKey:'traffic#actual',trafficShown:false}}));" . $cr;
      }
      if ($mapProp->traffic=='open'){
         $YaMapControls .= " l_YaMap" . $this::$YaMapNumber . ".controls.add(new ymaps.control.TrafficControl({state: {providerKey:'traffic#actual',trafficShown:true}}));" . $cr;
      }
      $YaMapCoords = '';
      if ($mapProp->showcoords=='true' or count($l_OnClickCoord)>0){
         if ($mapProp->showcoords=='true'){
            $YaMapCoords .= " var l_LayoutBtCoord = ymaps.templateLayoutFactory.createClass('<div>{{data.content|raw}}</div>');" . $cr;
            $YaMapCoords .= " var l_BtCoord = new ymaps.control.Button({data:{content:''}, options: {layout:l_LayoutBtCoord, selectOnClick:false}});" . $cr;
            $YaMapCoords .= " l_YaMap" . $this::$YaMapNumber . ".controls.add(l_BtCoord,{float:'none', position: {right:'10px', bottom:'55px'}});" . $cr;
         };
         if (count($l_OnClickCoord)>0){
            $YaMapCoords .= " var l_LayoutBtRoute = ymaps.templateLayoutFactory.createClass('<div>{{data.content|raw}}</div>');" . $cr;
            $YaMapCoords .= " var l_BtRoute = new ymaps.control.Button({data:{content:''},options: {layout:l_LayoutBtRoute, selectOnClick:false}});" . $cr;
            $YaMapCoords .= " l_YaMap" . $this::$YaMapNumber . ".controls.add(l_BtRoute,{float:'none', position: {left:'10px', bottom:'15px'}});" . $cr;
         };
         $YaMapCoords .= " l_YaMap" . $this::$YaMapNumber . ".events.add('click', function(e){" . $cr;
         $YaMapCoords .= "    var l_coords = e.get('coords');" . $cr;
         if ($mapProp->showcoords=='true'){
            $YaMapCoords .= "    var x = l_coords[0].toPrecision(8) + ',' + l_coords[1].toPrecision(8)" . $cr;
            $YaMapCoords .= "    l_BtCoord.data.set('content',x);" . $cr;
         }
         if (count($l_OnClickCoord)>0){
            $YaMapCoords .= " var l_restart = true;" . $cr;
            $YaMapCoords .= " while (l_restart==true){" . $cr;
            $YaMapCoords .= "    l_restart = false;" . $cr;
            $YaMapCoords .= "    l_YaMap" . $this::$YaMapNumber . ".geoObjects.each(function(p_geoObject){" . $cr;
            $YaMapCoords .= "       if (typeof p_geoObject.routeOptions!='undefined'){" . $cr;
            $YaMapCoords .= "          l_YaMap" . $this::$YaMapNumber . ".geoObjects.remove(p_geoObject);" . $cr;
            $YaMapCoords .= "         l_restart = true;" . $cr;
            $YaMapCoords .= "       }" . $cr;
            $YaMapCoords .= "       var l_prop = p_geoObject.properties.get('clickPlacemark');" . $cr;
            $YaMapCoords .= "       if (l_prop=='yes'){" . $cr;
            $YaMapCoords .= "          l_YaMap" . $this::$YaMapNumber . ".geoObjects.remove(p_geoObject);" . $cr;
            $YaMapCoords .= "          l_restart = true;" . $cr;
            $YaMapCoords .= "       }" . $cr;
            $YaMapCoords .= "    });" . $cr;
            $YaMapCoords .= " };" . $cr;
            $YaMapCoords .= " var clickPlacemark = new ymaps.Placemark(l_coords, {}, {preset: 'islands#redDotIcon'});" . $cr;
            $YaMapCoords .= " clickPlacemark.properties.set('clickPlacemark','yes');" . $cr;
            $YaMapCoords .= " l_YaMap" . $this::$YaMapNumber . ".geoObjects.add(clickPlacemark);" . $cr;
            $YaMapCoords .= " clickPlacemark.geometry.setCoordinates(l_coords);" . $cr;
            $YaMapCoords .= " clickPlacemark.properties.set('balloonContent','');" . $cr;
            $YaMapCoords .= " clickPlacemark.properties.set('hintContent','');" . $cr;
            $YaMapCoords .= " ymaps.geocode(l_coords).then(function(res){" . $cr;
            $YaMapCoords .= "    var firstGeoObject = res.geoObjects.get(0);" . $cr;
            $YaMapCoords .= "    clickPlacemark.properties.set('balloonContent',firstGeoObject.properties.get('text'));" . $cr;
            $YaMapCoords .= "    clickPlacemark.properties.set('hintContent',firstGeoObject.properties.get('name'));" . $cr;
            $YaMapCoords .= " });" . $cr;
            $l_color = 0;
            $YaMapCoords .= "    l_BtRoute.data.set('content','');" . $cr;
            foreach($l_OnClickCoord as $l_route){
               $l_color = $l_color + 1;
               $YaMapCoords .= " ymaps.route([{type:'wayPoint', point:clickPlacemark.geometry.getCoordinates()},";
               $YaMapCoords .= "              {type:'wayPoint', point:". $l_route ."}],";
               $YaMapCoords .= "              {mapStateAutoApply:false, avoidTrafficJams:true}).then(function(p_route){" . $cr;
               $YaMapCoords .= "       var points = p_route.getWayPoints();" . $cr;
               $YaMapCoords .= "       points.options.set('visible', false);" . $cr;
               switch ($l_color){
               case 1:
                  $l_strokeColor = '0000FF';
                  break;
               case 2:
                  $l_strokeColor = '00FF00';
                  break;
               case 3:
                  $l_strokeColor = 'FF0000';
                  break;
               default;
                  $l_strokeColor = 'FFFF00';
               }
               $l_span_color = '<span style="color:#'.$l_strokeColor.'";>';
               $YaMapCoords .= "        p_route.getPaths().options.set({strokeColor:'#" . $l_strokeColor . "', opacity: 0.5});" . $cr;
               $YaMapCoords .= "        l_YaMap" . $this::$YaMapNumber . ".geoObjects.add(p_route);" . $cr;
               $YaMapCoords .= "        var km = p_route.getHumanLength();" . $cr;
               $YaMapCoords .= "        var tm = p_route.getHumanTime();" . $cr;
               $YaMapCoords .= "    var x = l_BtRoute.data.get('content') + '<br>".$l_span_color."'+ km + '; ' + tm + '</span>';" . $cr;
               $YaMapCoords .= "    l_BtRoute.data.set('content',x);" . $cr;
               $YaMapCoords .= "    },this);";
            }
         }
         $YaMapCoords .= "   },this);";
      }
      $Z = <<<YaMapScript
<div id='yamap_div_{$this::$YaMapNumber}' style='width:{$mapProp->width}; height:{$mapProp->height}; margin:10px;' class='yamapapi'></div>
{$YaMapDescription}
YaMapScript;
      $Y = <<<YaMapScript
<script type="text/javascript">
ymaps.ready(VasuMap_{$this::$YaMapNumber});
function VasuMap_{$this::$YaMapNumber}(){
   {$YaMapBuilder}
   {$YaMapControls}
   var l_collection = new ymaps.GeoObjectCollection();
   {$YaMapRectangles}
   {$YaMapLabels}
   {$YaMapObj}
   if (l_collection.getLength()>0){
      l_YaMap{$this::$YaMapNumber}.geoObjects.add(l_collection);
   }
   {$YaMapGeo}
   {$YaMapSetBounds}
   {$YaMapRoute}
   {$YaMapCoords}
};
</script>
YaMapScript;
echo $Y;
return $Z;
   }
}
$wpYandexMapAPI = new wpYandexMapAPI();
remove_filter('the_content','wptexturize');
add_filter('the_posts',          array($wpYandexMapAPI,  'scanYaMapInPost'));
add_filter('the_content',        'do_shortcode');
add_action('wp_enqueue_scripts', array($wpYandexMapAPI,  'wpHead'));
add_shortcode('yandexMap',       array($wpYandexMapAPI,  'handleShortcodes'));
?>