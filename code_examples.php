1.    /* загрузчик в highoadblock */   
  
      CModule::IncludeModule('highloadblock');
		use Bitrix\Highloadblock as HL;
        use Bitrix\Main\Entity;
        $hlblock = HL\HighloadBlockTable::getById(1)->fetch();
        $entity = HL\HighloadBlockTable::compileEntity($hlblock);
        $entity_data_class = $entity->getDataClass();	
        
        $main_query = new Entity\Query($entity_data_class);
	    $main_query->setSelect(array('*'));
	    $main_query->setFilter(array('UF_STATUS'=>1));
	    $result = $main_query->exec();
        $result = new CDBResult($result);
	    if(!$row = $result->Fetch()){ // нет работающих загрузок
		
		//пишем запись о начале загрузке
		$data = array(
          "UF_BEGIN_DATE" => date("d.m.Y H:i:s"),
          "UF_STATUS" => 1,
		  "UF_RESULT" => '',
         );
        $result = $entity_data_class::add($data);
        $ID_HL = $result->getId(); 
     



$startAccountNum = isset($_GET['account']) ? (int)$_GET['account'] : 0;

$reader = new \XMLReader();


if (!$reader->open($_SERVER['DOCUMENT_ROOT'] . '/file.xml', NULL, LIBXML_NOEMPTYTAG)) {
	
	    // редактируем запись о завершении
		$data = array(
          "UF_END_DATE" => date("d.m.Y H:i:s"),
          "UF_STATUS" => 0,
		  "UF_RESULT" => 'Не найден xml файл',
         );
		$result = $entity_data_class::update($ID_HL, $data); 
	    exit();
} else {
	$data = xml2assoc($reader);
	$reader->close();
	unlink($_SERVER['DOCUMENT_ROOT'] . '/file.xml'); // удаляем файл
}



2. // Обработчик изменений/добавления данных пользователя
function OnAfterUserUpdateHandler(&$arFields)
{
global $USER_OPT_VAL,$OPT_GROUP;

//DEBUG
//pr($arFields);
	//Проверяем успешность операции изменения /добавления
	if(($arFields["RESULT"])&&($arFields["ID"]>0)){
		//ID пользователя
		$userID=$arFields["ID"];
		//Проверка передачи данных пользователя в параметрах
		if (array_key_exists("UF_TYPE",$arFields)) {
			//Проверка типа покупателя
			if ($arFields["UF_TYPE"]==$USER_OPT_VAL)
			{//Пользователь оптовик
				//Проверяем, не находится ли пользователь уже в группе оптовиков
				if (!isUserInGroup($userID,$OPT_GROUP))
				{
					//Если не принадлежит - добавляем
					CUser::SetUserGroup($userID, array_merge(CUser::GetUserGroup($userID), array($OPT_GROUP)));				
				}
			}
			else
			{//Пользователь не оптовик
				//Проверяем, находится ли пользователь в группе оптовиков
				if (isUserInGroup($userID,$OPT_GROUP))
				{
					//Если принадлежит - снимаем
					CUser::SetUserGroup($userID, array_diff(CUser::GetUserGroup($userID), array($OPT_GROUP)));				
				}
			}// /Проверка типа покупателя
		}// /Проверка передачи данных пользователя в параметрах
	}
}


3. /*  класс для работы с архивом файлов */

class Arch_Files{

  //protected static $pass_arch = ''; // пароль к архиву
  protected  static $pass_line =  '***'; // строка к паролю
  
  //public static $file_name = 'file_ar.zip'; // архив с файлами
  public static $file_name = 'file_ar.zip';
  
  public static $folder = 'temp_files'; // временная папка для распаковки

  public static function get_pass($name){
    return   md5( self::$pass_line);
  }	
  
  public static function Get_Files_From_Arch(){ // распаковываем архив
    
	if(!file_exists($_SERVER['DOCUMENT_ROOT'] . '/../xml/'.self::$folder))
	  mkdir($_SERVER['DOCUMENT_ROOT'] . '/../xml/'.self::$folder); 
    
	$zip = new ZipArchive; 

    if ($zip->open($_SERVER['DOCUMENT_ROOT'] . '/../xml/'.self::$file_name) === true)
    {
        if ($zip->setPassword(self::get_pass(self::$file_name)))
        {
            if(!$zip->extractTo($_SERVER['DOCUMENT_ROOT'] . '/../xml/'.self::$folder)){ // с кирилицей не работает
			 // echo $zip->getStatusString();
			}  
        }
        
        $zip->close();
    }
    else
    {
        //die("Failed opening archive: ". @$zip->getStatusString() . " (code: ". $zip_status .")");
    }
	
  }
  
  public static function Is_Arch_Exist(){ // проверяем на наличие архива
     if(file_exists($_SERVER['DOCUMENT_ROOT'] . '/../xml/'.self::$file_name))
 	    return true;
     else 
	    return false;
  }
  
  public static function Is_File_Exist($file){ // проверяем на наличие файла 
     if(file_exists($_SERVER['DOCUMENT_ROOT'] . '/../xml/'.self::$folder.'/'.$file))
 	    return true;
     else 
	    return false;
  }
  
  public static function Del_Temp_Dir(){  // удаление всех файлов 
    if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/../xml/'.self::$folder.'/'))
    foreach (glob($_SERVER['DOCUMENT_ROOT'] . '/../xml/'.self::$folder.'/*') as $file)
	  unlink($file);
  }
  
  public static function Get_File_Path(){
    return $_SERVER['DOCUMENT_ROOT'] . '/../xml/'.self::$folder.'/';
  } // удаление архива
  public static function Del_Arch(){
    unlink($_SERVER['DOCUMENT_ROOT'] . '/../xml/'.self::$file_name);
  }

4. // обновление свойства товара из 1с
	   //Получаем  реквизиты товара
		$db_props = CIBlockElement::GetProperty($IBLOCK_ID, $ELEMENT_ID, array(), Array("CODE"=>"CML2_TRAITS"));
		
		if($ar_props = $db_props->Fetch())
		{
			//ID характеристик из 1С
			$CML_TRAITS_ID=$ar_props['ID'];
		    $form = array();
			//Проходимся по всем характеристикам
			foreach($arFields['PROPERTY_VALUES'][$CML_TRAITS_ID] as $trait)
			{
			    
				//*** форма ***
				if ($trait['VALUE']=='Форма')
				{
					//CModule::IncludeModule("iblock");
					if($IBLOCK_ID==$RETAIL_CATALOG_OFFER_ID)
					   $SECTION_ID = 1111; // розница
					else   $SECTION_ID = 2222;  // отп
					$form_list = CIBlockElement::GetList(Array(), array("IBLOCK_ID"=>11, "NAME"=>$trait['DESCRIPTION'],'SECTION_ID'=>$SECTION_ID)); // поиск вставки в словаре
					if($ar_form = $form_list->GetNext())
					   $form["FORM"][] = $ar_form['ID'];
					else  // форма в словаре не найдена
					  {
					     // создаем в словаре новую форму
						 $new_form = new CIBlockElement;
						 $ar_new_form = Array(
                          "IBLOCK_SECTION_ID" => false,          // элемент лежит в корне раздела
                          "IBLOCK_ID"      => 11,
                          "NAME"           => $trait['DESCRIPTION'],
						  "IBLOCK_SECTION_ID" => $SECTION_ID,
                          "ACTIVE"         => "Y",            // активен
                          );
						 if($new_form_id = $new_form->Add($ar_new_form))
						     $form["FORM"][]=$new_from_id;
					  }
				}
			}
			if(count($form["FORM"])){ // найдены вставки
			CIBlockElement::SetPropertyValuesEx(
							$ELEMENT_ID,
							$IBLOCK_ID,
							$vstavki
					);
			}		
		}
------------------------------------------------------------


