<?php class Smarty_Internal_Template extends Smarty_Internal_Data{public $compiler_object=null;public $cacher_object=null;public $cache_id=null;public $compile_id=null;public $caching=null;public $cache_lifetime=null;public $cacher_class=null;public $caching_type=null;public $forceNocache=false;public $template_resource=null;public $resource_type=null;public $resource_name=null;private $isExisting=null;public $templateUid='';public $template_filepath=null;public $template_source=null;private $template_timestamp=null;private $compiled_filepath=null;public $compiled_template=null;private $compiled_timestamp=null;public $mustCompile=null;public $suppressHeader=false;public $suppressFileDependency=false;public $has_nocache_code=false;public $write_compiled_code=true;public $rendered_content=null;private $cached_filepath=null;public $cached_timestamp=null;private $isCached=null;private $cacheFileChecked=false;public $tpl_vars=array();public $parent=null;public $config_vars=array();public $plugin_data=array();public $properties=array('file_dependency'=>array(),'nocache_hash'=>'','function'=>array());public $required_plugins=array('compiled'=>array(),'nocache'=>array());public $saved_modifier=null;public $smarty=null;public $block_data=array();public $wrapper=null;public function __construct($template_resource,$smarty,$_parent=null,$_cache_id=null,$_compile_id=null,$_caching=null,$_cache_lifetime=null){$this->smarty=&$smarty;$this->cache_id=$_cache_id===null?$this->smarty->cache_id:$_cache_id;$this->compile_id=$_compile_id===null?$this->smarty->compile_id:$_compile_id;$this->caching=$_caching===null?$this->smarty->caching:$_caching;if($this->caching===true)$this->caching=Smarty::CACHING_LIFETIME_CURRENT;$this->cache_lifetime=$_cache_lifetime===null?$this->smarty->cache_lifetime:$_cache_lifetime;$this->parent=$_parent;$this->tpl_vars['smarty']=new Smarty_Variable;$this->template_resource=$template_resource;if($this->parent instanceof Smarty_Internal_Template){$this->block_data=$this->parent->block_data;}}public function getTemplateFilepath(){return $this->template_filepath===null?$this->template_filepath=$this->resource_object->getTemplateFilepath($this):$this->template_filepath;}public function getTemplateTimestamp(){return $this->template_timestamp===null?$this->template_timestamp=$this->resource_object->getTemplateTimestamp($this):$this->template_timestamp;}public function getTemplateSource(){if($this->template_source===null){if(!$this->resource_object->getTemplateSource($this)){throw new SmartyException("Unable to read template {$this->resource_type} '{$this->resource_name}'");}}return $this->template_source;}public function isExisting($error=false){if($this->isExisting===null){$this->isExisting=$this->resource_object->isExisting($this);}if(!$this->isExisting&&$error){throw new SmartyException("Unable to load template {$this->resource_type} '{$this->resource_name}'");}return $this->isExisting;}public function mustCompile(){$this->isExisting(true);if($this->mustCompile===null){$this->mustCompile=($this->resource_object->usesCompiler&&($this->smarty->force_compile||$this->resource_object->isEvaluated||$this->getCompiledTimestamp()===false||($this->smarty->compile_check&&$this->getCompiledTimestamp()<$this->getTemplateTimestamp())));}return $this->mustCompile;}public function getCompiledFilepath(){return $this->compiled_filepath===null?($this->compiled_filepath=!$this->resource_object->isEvaluated?$this->resource_object->getCompiledFilepath($this):false):$this->compiled_filepath;}public function getCompiledTimestamp(){return $this->compiled_timestamp===null?($this->compiled_timestamp=(!$this->resource_object->isEvaluated&&file_exists($this->getCompiledFilepath()))?filemtime($this->getCompiledFilepath()):false):$this->compiled_timestamp;}public function getCompiledTemplate(){if($this->compiled_template===null){if($this->mustCompile()){$this->compileTemplateSource();}else{if($this->compiled_template===null){$this->compiled_template=!$this->resource_object->isEvaluated&&$this->resource_object->usesCompiler?file_get_contents($this->getCompiledFilepath()):false;}}}return $this->compiled_template;}public function compileTemplateSource(){if(!$this->resource_object->isEvaluated){$this->properties['file_dependency']=array();$this->properties['file_dependency'][$this->templateUid]=array($this->getTemplateFilepath(),$this->getTemplateTimestamp(),$this->resource_type);}if($this->smarty->debugging){Smarty_Internal_Debug::start_compile($this);}if(!is_object($this->compiler_object)){$this->smarty->loadPlugin($this->resource_object->compiler_class);$this->compiler_object=new $this->resource_object->compiler_class($this->resource_object->template_lexer_class,$this->resource_object->template_parser_class,$this->smarty);}if($this->smarty->compile_locking&&!$this->resource_object->isEvaluated){if($saved_timestamp=$this->getCompiledTimestamp()){touch($this->getCompiledFilepath());}}try{$this->compiler_object->compileTemplate($this);}catch(Exception $e){if($this->smarty->compile_locking&&!$this->resource_object->isEvaluated&&$saved_timestamp){touch($this->getCompiledFilepath(),$saved_timestamp);}throw $e;}if(!$this->resource_object->isEvaluated&&$this->write_compiled_code){Smarty_Internal_Write_File::writeFile($this->getCompiledFilepath(),$this->compiled_template,$this->smarty);}if($this->smarty->debugging){Smarty_Internal_Debug::end_compile($this);}Smarty_Internal_TemplateCompilerBase::$_tag_objects=array();unset($this->compiler_object->parser->root_buffer,$this->compiler_object->parser->current_buffer,$this->compiler_object->parser,$this->compiler_object->lex,$this->compiler_object->template,$this->compiler_object);}public function getCachedFilepath(){return $this->cached_filepath===null?$this->cached_filepath=($this->resource_object->isEvaluated||!($this->caching==Smarty::CACHING_LIFETIME_CURRENT||$this->caching==Smarty::CACHING_LIFETIME_SAVED))?false:$this->cache_resource_object->getCachedFilepath($this):$this->cached_filepath;}public function getCachedTimestamp(){return $this->cached_timestamp===null?$this->cached_timestamp=($this->resource_object->isEvaluated||!($this->caching==Smarty::CACHING_LIFETIME_CURRENT||$this->caching==Smarty::CACHING_LIFETIME_SAVED))?false:$this->cache_resource_object->getCachedTimestamp($this):$this->cached_timestamp;}public function getCachedContent(){return $this->rendered_content===null?$this->rendered_content=($this->resource_object->isEvaluated||!($this->caching==Smarty::CACHING_LIFETIME_CURRENT||$this->caching==Smarty::CACHING_LIFETIME_SAVED))?false:$this->cache_resource_object->getCachedContents($this):$this->rendered_content;}public function writeCachedContent($content){if($this->resource_object->isEvaluated||!($this->caching==Smarty::CACHING_LIFETIME_CURRENT||$this->caching==Smarty::CACHING_LIFETIME_SAVED)){return false;}$this->properties['cache_lifetime']=$this->cache_lifetime;return $this->cache_resource_object->writeCachedContent($this,$this->createPropertyHeader(true).$content);}public function isCached($template=null,$cache_id=null,$compile_id=null,$parent=null){if($template===null){$no_render=true;}elseif($template===false){$no_render=false;}else{if($parent===null){$parent=$this;}$this->smarty->isCached($template,$cache_id,$compile_id,$parent);}if($this->isCached===null){$this->isCached=false;if(($this->caching==Smarty::CACHING_LIFETIME_CURRENT||$this->caching==Smarty::CACHING_LIFETIME_SAVED)&&!$this->resource_object->isEvaluated){$cachedTimestamp=$this->getCachedTimestamp();if($cachedTimestamp===false||$this->smarty->force_compile||$this->smarty->force_cache){return $this->isCached;}if($this->caching===Smarty::CACHING_LIFETIME_SAVED||($this->caching==Smarty::CACHING_LIFETIME_CURRENT&&(time()<=($cachedTimestamp + $this->cache_lifetime)||$this->cache_lifetime<0))){if($this->smarty->debugging){Smarty_Internal_Debug::start_cache($this);}$this->rendered_content=$this->cache_resource_object->getCachedContents($this,$no_render);if($this->smarty->debugging){Smarty_Internal_Debug::end_cache($this);}if($this->cacheFileChecked){$this->isCached=true;return $this->isCached;}$this->cacheFileChecked=true;if($this->caching===Smarty::CACHING_LIFETIME_SAVED&&$this->properties['cache_lifetime']>=0&&(time()>($this->getCachedTimestamp()+ $this->properties['cache_lifetime']))){$this->tpl_vars=array();$this->rendered_content=null;return $this->isCached;}if(!empty($this->properties['file_dependency'])&&$this->smarty->compile_check){$resource_type=null;$resource_name=null;foreach($this->properties['file_dependency'] as $_file_to_check){If($_file_to_check[2]=='file'||$_file_to_check[2]=='extends'||$_file_to_check[2]=='php'){$mtime=filemtime($_file_to_check[0]);}else{$this->getResourceTypeName($_file_to_check[0],$resource_type,$resource_name);$resource_handler=$this->loadTemplateResourceHandler($resource_type);$mtime=$resource_handler->getTemplateTimestampTypeName($resource_type,$resource_name);}If($mtime>$_file_to_check[1]){$this->tpl_vars=array();$this->rendered_content=null;return $this->isCached;}}}$this->isCached=true;}}}return $this->isCached;}public function renderTemplate(){if($this->resource_object->usesCompiler){if($this->mustCompile()&&$this->compiled_template===null){$this->compileTemplateSource();}if($this->smarty->debugging){Smarty_Internal_Debug::start_render($this);}$_smarty_tpl=$this;ob_start();if($this->resource_object->isEvaluated){eval("?>".$this->compiled_template);}else{include($this->getCompiledFilepath());if($this->smarty->compile_check){if(!empty($this->properties['file_dependency'])){$this->mustCompile=false;$resource_type=null;$resource_name=null;foreach($this->properties['file_dependency'] as $_file_to_check){If($_file_to_check[2]=='file'||$_file_to_check[2]=='extends'||$_file_to_check[2]=='php'){$mtime=filemtime($_file_to_check[0]);}else{$this->getResourceTypeName($_file_to_check[0],$resource_type,$resource_name);$resource_handler=$this->loadTemplateResourceHandler($resource_type);$mtime=$resource_handler->getTemplateTimestampTypeName($resource_type,$resource_name);}If($mtime>$_file_to_check[1]){$this->mustCompile=true;break;}}if($this->mustCompile){ob_get_clean();$this->compileTemplateSource();ob_start();include($this->getCompiledFilepath());}}}}}else{if(is_callable(array($this->resource_object,'renderUncompiled'))){if($this->smarty->debugging){Smarty_Internal_Debug::start_render($this);}ob_start();$this->resource_object->renderUncompiled($this);}else{throw new SmartyException("Resource '$this->resource_type' must have 'renderUncompiled' methode");}}$this->rendered_content=ob_get_clean();if(!$this->resource_object->isEvaluated&&empty($this->properties['file_dependency'][$this->templateUid])){$this->properties['file_dependency'][$this->templateUid]=array($this->getTemplateFilepath(),$this->getTemplateTimestamp(),$this->resource_type);}if($this->parent instanceof Smarty_Internal_Template){$this->parent->properties['file_dependency']=array_merge($this->parent->properties['file_dependency'],$this->properties['file_dependency']);foreach($this->required_plugins as $code=>$tmp1){foreach($tmp1 as $name=>$tmp){foreach($tmp as $type=>$data){$this->parent->required_plugins[$code][$name][$type]=$data;}}}}if($this->smarty->debugging){Smarty_Internal_Debug::end_render($this);}if(!$this->resource_object->isEvaluated&&($this->caching==Smarty::CACHING_LIFETIME_SAVED||$this->caching==Smarty::CACHING_LIFETIME_CURRENT)){if($this->smarty->debugging){Smarty_Internal_Debug::start_cache($this);}$this->properties['has_nocache_code']=false;$cache_split=preg_split("!/\*%%SmartyNocache:{$this->properties['nocache_hash']}%%\*\/(.+?)/\*/%%SmartyNocache:{$this->properties['nocache_hash']}%%\*/!s",$this->rendered_content);preg_match_all("!/\*%%SmartyNocache:{$this->properties['nocache_hash']}%%\*\/(.+?)/\*/%%SmartyNocache:{$this->properties['nocache_hash']}%%\*/!s",$this->rendered_content,$cache_parts);$output='';foreach($cache_split as $curr_idx=>$curr_split){$output.=preg_replace('/(<%|%>|<\?php|<\?|\?>)/','<?php echo \'$1\'; ?>',$curr_split);if(isset($cache_parts[0][$curr_idx])){$this->properties['has_nocache_code']=true;$output.=preg_replace("!/\*/?%%SmartyNocache:{$this->properties['nocache_hash']}%%\*/!",'',$cache_parts[0][$curr_idx]);}}if(isset($this->smarty->autoload_filters['output'])||isset($this->smarty->registered_filters['output'])){$output=Smarty_Internal_Filter_Handler::runFilter('output',$output,$this);}$_smarty_tpl=$this;ob_start();eval("?>".$output);$this->rendered_content=ob_get_clean();$this->writeCachedContent('<?php if (!$no_render) {?>'.$output.'<?php } ?>');if($this->smarty->debugging){Smarty_Internal_Debug::end_cache($this);}}else{if($this->has_nocache_code&&!empty($this->properties['nocache_hash'])&&!empty($this->parent->properties['nocache_hash'])){$this->rendered_content=preg_replace("/{$this->properties['nocache_hash']}/",$this->parent->properties['nocache_hash'],$this->rendered_content);$this->parent->has_nocache_code=$this->has_nocache_code;}}}public function getRenderedTemplate(){if($this->resource_object->isEvaluated){$this->caching=false;}$this->isExisting(true);if($this->rendered_content===null){if($this->isCached){if($this->smarty->debugging){Smarty_Internal_Debug::start_cache($this);}$this->rendered_content=$this->cache_resource_object->getCachedContents($this,false);if($this->smarty->debugging){Smarty_Internal_Debug::end_cache($this);}}if($this->isCached===null){$this->isCached(false);}if(!$this->isCached){$this->renderTemplate();}}$this->updateParentVariables();$this->isCached=null;return $this->rendered_content;}public function parseResourceName($template_resource,&$resource_type,&$resource_name,&$resource_handler){if(empty($template_resource))return false;$this->getResourceTypeName($template_resource,$resource_type,$resource_name);$resource_handler=$this->loadTemplateResourceHandler($resource_type);if($resource_type!='eval'){$this->smarty->template_objects[sha1($this->template_resource.$this->cache_id.$this->compile_id)]=$this;}return true;}public function buildTemplateFilepath($file=null){if($file==null){$file=$this->resource_name;}if(!preg_match('/^([\/\\\\]|[a-zA-Z]:[\/\\\\])/',$file)){foreach((array)$this->smarty->template_dir as $_template_dir){if(strpos('/\\',substr($_template_dir,-1))===false){$_template_dir.=DS;}$_filepath=$_template_dir.$file;if(file_exists($_filepath)){return $_filepath;}if(!preg_match('/^([\/\\\\]|[a-zA-Z]:[\/\\\\])/',$_template_dir)){if(($_filepath=Smarty_Internal_Get_Include_Path::getIncludePath($_filepath))!==false){return $_filepath;}}}}if(file_exists($file))return $file;if(!empty($this->smarty->default_template_handler_func)){if(!is_callable($this->smarty->default_template_handler_func)){throw new SmartyException("Default template handler not callable");}else{$_return=call_user_func_array($this->smarty->default_template_handler_func,array($this->resource_type,$this->resource_name,&$this->template_source,&$this->template_timestamp,$this));if(is_string($_return)){return $_return;}elseif($_return===true){return $file;}}}return false;}public function updateParentVariables($scope=Smarty::SCOPE_LOCAL){$has_root=false;foreach($this->tpl_vars as $_key=>$_variable){$_variable_scope=$this->tpl_vars[$_key]->scope;if($scope==Smarty::SCOPE_LOCAL&&$_variable_scope==Smarty::SCOPE_LOCAL){continue;}if(isset($this->parent)&&($scope==Smarty::SCOPE_PARENT||$_variable_scope==Smarty::SCOPE_PARENT)){if(isset($this->parent->tpl_vars[$_key])){$this->parent->tpl_vars[$_key]->value=$this->tpl_vars[$_key]->value;}else{$this->parent->tpl_vars[$_key]=clone $_variable;$this->parent->tpl_vars[$_key]->scope=Smarty::SCOPE_LOCAL;}}if($scope==Smarty::SCOPE_ROOT||$_variable_scope==Smarty::SCOPE_ROOT){if($this->parent==null){continue;}if(!$has_root){$root_ptr=$this;while($root_ptr->parent!=null){$root_ptr=$root_ptr->parent;$has_root=true;}}if(isset($root_ptr->tpl_vars[$_key])){$root_ptr->tpl_vars[$_key]->value=$this->tpl_vars[$_key]->value;}else{$root_ptr->tpl_vars[$_key]=clone $_variable;$root_ptr->tpl_vars[$_key]->scope=Smarty::SCOPE_LOCAL;}}if($scope==Smarty::SCOPE_GLOBAL||$_variable_scope==Smarty::SCOPE_GLOBAL){if(isset(Smarty::$global_tpl_vars[$_key])){Smarty::$global_tpl_vars[$_key]->value=$this->tpl_vars[$_key]->value;}else{Smarty::$global_tpl_vars[$_key]=clone $_variable;}Smarty::$global_tpl_vars[$_key]->scope=Smarty::SCOPE_LOCAL;}}}protected function getResourceTypeName($template_resource,&$resource_type,&$resource_name){if(strpos($template_resource,':')===false){$resource_type=$this->smarty->default_resource_type;$resource_name=$template_resource;}else{list($resource_type,$resource_name)=explode(':',$template_resource,2);if(strlen($resource_type)==1){$resource_type='file';$resource_name=$template_resource;}}}protected function loadTemplateResourceHandler($resource_type){if(isset($this->smarty->registered_resources[$resource_type])){return new Smarty_Internal_Resource_Registered($this);}else{if(in_array($resource_type,array('file','string','extends','php','stream','eval'))){$_resource_class='Smarty_Internal_Resource_'.ucfirst($resource_type);return new $_resource_class($this->smarty);}else{$_resource_class='Smarty_Resource_'.ucfirst($resource_type);if($this->smarty->loadPlugin($_resource_class)){if(class_exists($_resource_class,false)){return new $_resource_class($this->smarty);}else{return new Smarty_Internal_Resource_Registered($this,$resource_type);}}else{$_known_stream=stream_get_wrappers();if(in_array($resource_type,$_known_stream)){if(is_object($this->smarty->security_policy)){$this->smarty->security_policy->isTrustedStream($resource_type);}return new Smarty_Internal_Resource_Stream($this->smarty);}else{throw new SmartyException('Unkown resource type \''.$resource_type.'\'');}}}}}public function createPropertyHeader($cache=false){$plugins_string='';if(!$cache){if(!empty($this->required_plugins['compiled'])){$plugins_string='<?php ';foreach($this->required_plugins['compiled'] as $tmp){foreach($tmp as $data){$plugins_string.="if (!is_callable('{$data['function']}')) include '{$data['file']}';\n";}}$plugins_string.='?>';}if(!empty($this->required_plugins['nocache'])){$this->has_nocache_code=true;$plugins_string.="<?php echo '/*%%SmartyNocache:{$this->properties['nocache_hash']}%%*/<?php ";foreach($this->required_plugins['nocache'] as $tmp){foreach($tmp as $data){$plugins_string.="if (!is_callable(\'{$data['function']}\')) include \'{$data['file']}\';\n";}}$plugins_string.="?>/*/%%SmartyNocache:{$this->properties['nocache_hash']}%%*/';?>\n";}}$this->properties['has_nocache_code']=$this->has_nocache_code;$properties_string="<?php /*%%SmartyHeaderCode:{$this->properties['nocache_hash']}%%*/";if($this->smarty->direct_access_security){$properties_string.="if(!defined('SMARTY_DIR')) exit('no direct access allowed');\n";}if($cache){unset($this->properties['function']);if(!empty($this->smarty->template_functions)){foreach($this->smarty->template_functions as $name=>$function_data){if(isset($function_data['called_nocache'])){unset($function_data['called_nocache'],$this->smarty->template_functions[$name]['called_nocache']);$this->properties['function'][$name]=$function_data;}}}}$properties_string.="\$_smarty_tpl->decodeProperties(".var_export($this->properties,true)."); /*/%%SmartyHeaderCode%%*/?>\n";return $properties_string.$plugins_string;}public function decodeProperties($properties){$this->has_nocache_code=$properties['has_nocache_code'];$this->properties['nocache_hash']=$properties['nocache_hash'];if(isset($properties['cache_lifetime'])){$this->properties['cache_lifetime']=$properties['cache_lifetime'];}if(isset($properties['file_dependency'])){$this->properties['file_dependency']=array_merge($this->properties['file_dependency'],$properties['file_dependency']);}if(!empty($properties['function'])){$this->properties['function']=array_merge($this->properties['function'],$properties['function']);$this->smarty->template_functions=array_merge($this->smarty->template_functions,$properties['function']);}}public function createLocalArrayVariable($tpl_var,$nocache=false,$scope=Smarty::SCOPE_LOCAL){if(!isset($this->tpl_vars[$tpl_var])){$tpl_var_inst=$this->getVariable($tpl_var,null,true,false);if($tpl_var_inst instanceof Undefined_Smarty_Variable){$this->tpl_vars[$tpl_var]=new Smarty_variable(array(),$nocache,$scope);}else{$this->tpl_vars[$tpl_var]=clone $tpl_var_inst;if($scope!=Smarty::SCOPE_LOCAL){$this->tpl_vars[$tpl_var]->scope=$scope;}}}if(!(is_array($this->tpl_vars[$tpl_var]->value)||$this->tpl_vars[$tpl_var]->value instanceof ArrayAccess)){settype($this->tpl_vars[$tpl_var]->value,'array');}}public function _count($value){if(is_array($value)===true||$value instanceof Countable){return count($value);}elseif($value instanceof Iterator){$value->rewind();if($value->valid()){return iterator_count($value);}}elseif($value instanceof PDOStatement){return $value->rowCount();}elseif($value instanceof Traversable){return iterator_count($value);}elseif($value instanceof ArrayAccess){if($value->offsetExists(0)){return 1;}}elseif(is_object($value)){return count($value);}return 0;}public function fetch($template=null,$cache_id=null,$compile_id=null,$parent=null,$display=false){if($template==null){return $this->smarty->fetch($this);}else{if(!isset($parent)){$parent=$this;}return $this->smarty->fetch($template,$cache_id,$compile_id,$parent,$display);}}public function display($template=null,$cache_id=null,$compile_id=null,$parent=null){if($template==null){return $this->smarty->display($this);}else{if(!isset($parent)){$parent=$this;}return $this->smarty->display($template,$cache_id,$compile_id,$parent);}}public function __set($property_name,$value){if($property_name=='resource_object'||$property_name=='cache_resource_object'){$this->$property_name=$value;}elseif(property_exists($this->smarty,$property_name)){$this->smarty->$property_name=$value;}else{throw new SmartyException("invalid template property '$property_name'.");}}public function __get($property_name){if($property_name=='resource_object'){$this->resource_object=null;if(!$this->parseResourceName($this->template_resource,$this->resource_type,$this->resource_name,$this->resource_object)){throw new SmartyException("Unable to parse resource name \"{$template_resource}\"");}return $this->resource_object;}if($property_name=='cache_resource_object'){$this->cache_resource_object=$this->loadCacheResource();return $this->cache_resource_object;}if(property_exists($this->smarty,$property_name)){return $this->smarty->$property_name;}else{throw new SmartyException("template property '$property_name' does not exist.");}}public function __call($name,$args){static $camel_func;if(!isset($camel_func))$camel_func=create_function('$c','return "_" . strtolower($c[1]);');$first3=strtolower(substr($name,0,3));if(in_array($first3,array('set','get'))&&substr($name,3,1)!=='_'){$property_name=strtolower(substr($name,3,1)).substr($name,4);$property_name=preg_replace_callback('/([A-Z])/',$camel_func,$property_name);if(property_exists($this,$property_name)){if($first3=='get')return $this->$property_name;else return $this->$property_name=$args[0];}}if(strpos($name,'_')!==false){if(!isset($this->wrapper)){$this->wrapper=new Smarty_Internal_Wrapper($this);}return $this->wrapper->convert($name,$args);}return call_user_func_array(array($this->smarty,$name),$args);}}?>