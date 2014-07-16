<?php

set_time_limit(3);

class bnf{
    
       private  $delimiter =':';   
       
       private  $parse_start_var ='start';
    
       private  $next_tree_id=0;
       
       private  $limit_counter=300;
    
       private  $tree=array();
    
public   function __construct(){
        
}     

private  function  grammer_start_protocol($str){
    
    return ctype_alnum($str) || $str=='-' || $str=='_';
    
} 
private  function  strip_space_comment($str='',$size=0,$i=0,&$line_count=0){ 
    $line_count=0;
    
    
    
    
   while($i<$size&&ord($str[$i])<=32)
   {
       if(ord($str[$i])==10)
       {
           $line_count++;
       }
       $i++;
   }
    
  $is_exist_comment=1;
  while($i<$size && $is_exist_comment)
  {  
      $is_exist_comment=0;
      
     if(substr($str,$i,1) == '#')
     {   
         $i++;
         while($i<$size ){
       
           if($str[$i] == "\n")
           {   
               $is_exist_comment=1;
               break;
           }    
      
           $i++;
       
         }  
     }elseif(substr($str,$i,2) == '//')
     {
         $i+=2;
         while($i<$size ){
       
           if($str[$i] == "\n")
           {   
               $is_exist_comment=1;
               break;
           }    
      
           $i++;
       
         }  
     }elseif(substr($str,$i,2) == '/*')
     {
         $i+=2;
         while($i<$size ){
       
           if(substr($str,$i,2) == "*/")
           {   
               $i+=2;
               $is_exist_comment=1;
               break;
           }    
      
           $i++;
       
         }  
     }
   if(!$is_exist_comment){
       break;
   } 
    
  } 
    
    
    
    
    
    
    
    
      while($i<$size&&ord($str[$i])<=32)
   {
       if(ord($str[$i])==10)
       {
           $line_count++;
       }
       $i++;
   }
   
   
   
   return $i; 
} 
public   function  bnf_rule_parser($str=''){
    
    $i=0; 
    $strlen=strlen($str);
    $flag_grammer_starter=0;
    $line=1;
    $grammer=array();
    
    while($i  <  $strlen) 
    {
        
      $i = $this->strip_space_comment($str,$strlen,$i,$t_line) ;  // is space , newline , tab , ....
      $line += $t_line;
      
      
       if( !($i<$strlen)){break;}
       
       
      $grammer_start_name='';
      
         while($i < $strlen && $this->grammer_start_protocol($str[$i]))
         {
              
              
            $grammer_start_name .= $str[$i]; 

             $i++;
         } 
          
         
            if(!strlen($grammer_start_name))
            {    
                die('error on line '.$line);exit;
            }
          
          
            $i = $this->strip_space_comment($str,$strlen,$i,$t_line) ;  // is space , newline , tab , ....
            $line += $t_line;
      
         if( substr($str,$i,strlen($this->delimiter)) != $this->delimiter)   // 's : AB' ;   ':' is delimiter
         {
              die('error on line '.$line);
             
         }
          
          $i += strlen($this->delimiter);
          
          
           $i = $this->strip_space_comment($str,$strlen,$i,$t_line) ;  // is space , newline , tab , ....
           $line += $t_line;
          
          $array_piece=array();
          $piece_counter=0;
          $flag_true_ended=0;
          
          
          
         while($i < $strlen )
         {     
              
             
            
           
           
           
           
              if( $this->grammer_start_protocol($str[$i]) )
              {
                  $t_grammer_var_name='';
                 while($i <$strlen && $this->grammer_start_protocol($str[$i]) )
                 {
                   $t_grammer_var_name .= $str[$i];
                   $i++;  
                 } 
                 
                 $i--;
                 
                 $array_piece [$piece_counter][]= array(
                                                         'type'=>'var' , 
                                                         'value' => $t_grammer_var_name
                                                        );
  
                  
              }
              elseif($str[$i] == "'")
              {
                 $i++;
                 $t_piece='';
                 while($i < $strlen )
                 {
                    
                     if($str[$i] == "'" && $str[$i-1] != '\\'){ break;}
                     
                    $t_piece .= $str[$i];
                    $i++; 
                     
                 }
                   
                 if( !(@$str[$i]== "'" && @$str[$i-1] != '\\'))
                 {
                    die('error on line '.$line.' unexpected string token'); 
                 }
                 
                
                $array_piece [$piece_counter][]= array('type'=>'string' , 'value' => $t_piece);
                
                 
              }
              elseif($str[$i] == '"')
              {
                   $i++;
                 $t_piece='';
                 while($i < $strlen )
                 {
                    
                     if($str[$i] == '"' && $str[$i-1] != '\\'){ break;}
                     
                    $t_piece .= $str[$i];
                    $i++; 
                     
                 }
                   
                 if( !(@$str[$i]== '"' && @$str[$i-1] != '\\'))
                 {
                    die('error on line '.$line.' unexpected string token'); 
                 }
                 
                
                $array_piece [$piece_counter][]= array('type'=>'string' , 'value' => $t_piece);
                 
  
              }
              elseif($str[$i] == '|')
              {
                $piece_counter++;
              }
              elseif($str[$i] == ';')
              {                           
                 
                  if(empty($array_piece))
                  {
                      die('error on line '.$line.' unexpected token ";"')     ;
                  }    
              
              
                  if(isset($grammer [$grammer_start_name]) && count($grammer [$grammer_start_name])) 
                  {
                      
                         $ttt=array();
                    foreach($array_piece as $kk=>$vv){
                       
                       
                       foreach($vv as $kkk=>$vvv){
                       
                        $ttt[]=$vvv;
                       }
                       
                   }
                   
                     $grammer [$grammer_start_name][] = $ttt;
                      
                  }else
                  {
                    $grammer [$grammer_start_name] = $array_piece;  
                  }
                   
                  
                  
                  $flag_true_ended=1;    
                  break;                                     
              }
              elseif($str[$i] == '\\' && ($i+1)<$strlen &&($str[$i+1] == 'U'  ||  $str[$i+1] == 'u'))
              {
                 $i+=2;
                  $t_piece='';
                 while($i<$strlen && $str[$i]>='0'&&$str[$i]<='9' || $str[$i]>='a'&&$str[$i]<='f' || $str[$i]>='A'&&$str[$i]<='F') 
                 {
                    $t_piece .= $str[$i]; 
                    $i++;
                 } 
                 
                 if(!strlen($t_piece))
                 {
                    die('error on line '.$line.' unexpected  token "\u" '); 
                 }
                 
                     $i = $this->strip_space_comment($str,$strlen,$i,$t_line) ;  // is space , newline , tab , ....
                     $line += $t_line;
                   if($str[$i] == '.' && ($i+1) < $strlen &&$str[$i+1] == '.'  )
                  {
                     $i+=2;  
                     $t_piece2='';
                      
                     if($str[$i] == '\\' && ($i+1)<$strlen &&($str[$i+1] == 'U'  ||  $str[$i+1] == 'u'))
                     {
                       $i+=2;
                  
                       while($i<$strlen && $str[$i]>='0'&&$str[$i]<='9' || $str[$i]>='a'&&$str[$i]<='f' || $str[$i]>='A'&&$str[$i]<='F') 
                       {
                          $t_piece2 .= $str[$i]; 
                          $i++;
                       } $i--;
                 
               
                      }
                  
                       if(!strlen($t_piece2))
                      {
                        die('error on line '.$line.' unexpected  token ".." "\u" '); 
                      }
                  
                  
                     $array_piece [$piece_counter][]= array('type'=>'range' ,'from'=>$t_piece ,'to' => $t_piece2);
                     
                }else
                {
                   $array_piece [$piece_counter][]= array('type'=>'char' , 'value' => $t_piece);
                   $i--;
                }
                 
                 
                  
              }
              else
              {
                   die('error on line '.$line.' unexpected  token "'.$str[$i].'"'); 
              }
             
             
             
             
             $i++;
             
             
              $i = $this->strip_space_comment($str,$strlen,$i,$t_line) ;  // is space , newline , tab , ....
              $line += $t_line;
             
         }
         
        if(! $flag_true_ended)
        {
           die('error on line '.$line.' unexpected  token ";" for endding rull');  
        }
        
        
      
      
      $i++;  
    } 
        //var_dump($grammer['start']) ;exit;
        return ($grammer);
  }  // end func parser
public   function  parse_old($rule='',$source=''){
    
 $G=$this->bnf_rule_parser($rule);
   // var_dump($G) ;
 $parse_tree=array();
 $stack=array();
 $source_size= strlen($source);
 $temp_continue_i=0;
 
                   
 
 
 
  if(! isset($G[$this->parse_start_var]))
  {
      die ('error parse . not exist start var "'.$this->parse_start_var.'" .');
  }
 
 $stack[]=array('var'=>$this->parse_start_var ,  'c1'=>0, 'c2'=>0,'i'=>0 , 'i-temp'=>0 ,'true'=>0);
 
 $counter=0;
 
 
 
 while(!empty($stack))
 {    
     var_dump($stack); echo '<hr>' ;
     
     $pop_stack = array_pop($stack);
     $counter++;
      
      
     $currect_is_true   = $pop_stack['true'];
     $currect_var  = $pop_stack['var'] ;
     $currect_data = $G[$currect_var] ;
     $col1_index   = $pop_stack['c1'];
     $col2_index   = $pop_stack['c2'];
     $continue_i   = $pop_stack['i'];
     $temp_continue_i = $pop_stack['i-temp'] ; 
  
  
   
  
  
 
     
    if($currect_is_true == 1)
    {  
      
      if(!isset($currect_data[$col1_index][$col2_index]))
      {
        
          if(!empty($stack ))
          {  
             $temp_pop_stack=array_pop($stack);
              
              $temp_pop_stack['i-temp'] = $temp_continue_i; 
              $temp_pop_stack['true']   = $currect_is_true;
              
              $stack []= $temp_pop_stack;
           
           }    
        
         continue;    
       }  
    }
    

    
    
    
    
    
     
     if(!($temp_continue_i < $source_size))
     {
        // continue;
     }  
      // var_dump($currect_data); 
     // var_dump($currect_var);
     
     
     
   //  var_dump($currect_var,$col1_index,$col2_index);         echo '<hr>';
     if($counter>=20){
         
         exit;
     }
     
      
        
     while( isset($currect_data[$col1_index]))
     {
         
         
         while($temp_continue_i < $source_size && isset($currect_data[$col1_index][$col2_index]))
        {
           $match_strings=0;       
           $item=$currect_data[$col1_index][$col2_index];
          //  var_dump($item);
           if($item['type'] == 'string')
           {
             $str1=$item['value'] ;  
             $str2=substr($source,$temp_continue_i,strlen($item['value']));  
               
               
              if(strlen($str1)==strlen($str2)&& strlen($str1) && $item['value'] == substr($source,$temp_continue_i,strlen($item['value'])))
              {
                         
                    var_dump('yes#'.$currect_var.'#temp-i='.$temp_continue_i.'#'.$str1.'=='.$str2.'@end='.$source_size) ;  
                 
                 
                  $temp_continue_i += strlen($str1) ; 
                  $match_strings=1;

                  
                    
                                
                              //  die('end . accept input') ;
                   
                     if( ! isset($currect_data[$col1_index][$col2_index+1]))  // not continue in piece 
                    {
                                                  
                       if(!empty($stack))
                       {
                             $temp_pop_stack =array_pop($stack);
                         
                             $temp_pop_stack['i-temp'] = $temp_continue_i;
                             
                             
                             $temp_pop_stack['true']   =   1; 
                             
                                                     
                             $stack []= $temp_pop_stack;
                              
                      
                       }
                      
                      
                      
                       break(2);
                  }
             
                  
                     
              }
              else
              {      
                  var_dump('not#'.$currect_var.'#temp-i='.$temp_continue_i.'#'.$str1.'=='.$str2) ;
                  //var_dump($col1_index,$col2_index);
                  break;
              } 
               
               
           }
           elseif($item['type'] == 'var')
           {
              if( isset ($G[$item['value']]) )
              {
                    var_dump($item['value'],$currect_var);
                    
                  $stack []= array('var'=>$currect_var   , 'c1'=> $col1_index  ,'c2'=>$col2_index+1, 'i'=>$continue_i      , 'i-temp'=>$temp_continue_i , 'true'=>0 )  ;
                  $stack []= array('var'=>$item['value'] , 'c1'=>0             ,'c2'=>0            , 'i'=>$temp_continue_i , 'i-temp'=>$temp_continue_i , 'true'=>0)  ;
                  
                  
                  break(2);  
                  
              }else{
                  die('parse error . undefined var "'.$item['value'].'" ');
              } 
               
               
           }
           
        
         
           $col2_index++; 
       }
          $col2_index=0;
          if(isset($currect_data[$col1_index+1]))
          {
             
              $temp_continue_i =$continue_i;
          }
           if(!$match_strings)
           {
               //   array_pop($stack);
           }
         
        $col1_index++; 
     }
     
     
     
     
     
     
 }
 
   if($temp_continue_i == $source_size)
   {
       die('<div style="color:blue;font-size:30px;">yes accept input string.</div>') ;
   }
 
 
  var_dump('end while.',$stack,$temp_continue_i);
    
      
    
    
} // end func
private  function  next_tree_id(){
    return $this->next_tree_id++;
}
private  function  tree_childs($parentid=0){
    
   $childs=array();
  foreach($this->tree as $tree_key=>$tree_value)
  {
      
     if($tree_value['parentid'] == $parentid)
     {
         $childs []= $tree_value;
     } 
      
  } 
   
   
   
   
  
   return $childs;
   
   
}
private  function  tree_childs_by_part($parentid=0,$part=0){
    
   $childs=array();
  foreach($this->tree as $tree_key=>$tree_value)
  {
      
     if($tree_value['parentid'] == $parentid && $tree_value['part'] == $part)
     {
         $childs []= $tree_value;
     } 
      
  } 
   
   
   
   
  
   return $childs;
   
   
}
private  function  delete_childs($parentid=0){
    
   foreach($this->tree as $tree_key=>$tree_value)
  {  
      
     if($tree_value['parentid'] == $parentid)
     {
         $this->delete_childs($tree_value['id']);
         unset($this->tree[$tree_key]);
     } 
      
  } 
   
   
   return 1;
   
   
}
private  function  delete_childs_by_part($parentid=0,$part=0){
    
   foreach($this->tree as $tree_key=>$tree_value)
  {  
      
     if($tree_value['parentid'] == $parentid && $tree_value['part'] == $part)
     {
         $this->delete_childs($tree_value['id']);
         unset($this->tree[$tree_key]);
     } 
      
  } 
   
   
   return 1;
   
   
}
private  function  temp_continue_i($id=0,$part=0){
    
    $childs=$this->tree_childs_by_part($id,$part);
    $find=-1;
   foreach($childs as $tree_key=>$tree_value)
  {  
      
    
         $find = $tree_value['i-temp'];
      
  } 
   
   if($find != -1)
   {
       return $find;
   }
   return $this->tree[$id]['i'];
   
   
}
public   function  parse($rule='',$source=''){
    
 $G=$this->bnf_rule_parser($rule);
   // var_dump($G) ;
 $parse_tree=array();
 $stack=array();
 $source_size= strlen($source);
 $temp_continue_i=0;
 $limit_counter=$this->limit_counter;
   $this->tree = array();
 
 
  if(! isset($G[$this->parse_start_var]))
  {
      die ('error parse . not exist start var "'.$this->parse_start_var.'" .');
  }
  $first_node_id=$this->next_tree_id();
 
 
// $stack[]=array('id'=>$first_node_id ,  'c1'=>0, 'c2'=>0 ,'i'=>0 , 'i-temp'=>0 );
 $stack[]=array('id'=>$first_node_id ,'c1'=>0, 'c2'=>0);
 
 $this->tree[$first_node_id]= array('id'=>$first_node_id,'part'=>0 ,'type'=>'var','value'=>$this->parse_start_var,'match'=>''  ,'i'=>0 , 'i-temp'=>0 ,'true'=>-1 ,'parentid'=> -1);
 
 $counter=0;
 
 
  befor_stack:
 
 
 
 
 while(!empty($stack))
 {    
    //var_dump($stack,$this->tree); echo '<hr>' ;
       
     $pop_stack = array_pop($stack);
     $counter++;
     $currect_node_id = $pop_stack ['id'];
     
     
      if(!isset($this->tree[$currect_node_id])){ continue;die('parse error.') ;}
      
      
      
     $node           = $this->tree[$currect_node_id]; 
     $node_type      = $node['type']; 
     $node_is_true   = $node['true']; 
     
     
     
    // if($node_is_true == 0 ){ continue; }
      
      
      $node_var      = $node['value']; 
      $node_G_data   = $G[$node_var]; 
      
      
      $node_id  =       $node['id']; 
      $node_parentid  = $node['parentid']; 
      
        
      $col1_index     = $pop_stack['c1'];
      $col2_index     = $pop_stack['c2'];
      $currect_part   = $col1_index;
            
     $continue_i      =  $node['i'] ; 
     $temp_continue_i =  $this->temp_continue_i($node_id,$currect_part);
     
   //  var_dump($temp_continue_i,$continue_i);
   // var_dump($this->tree_childs_by_part($node_id,$currect_part));
     $node_childs=$this->tree_childs($node_id);
   
          //   echo '<hr>';                
              
    //var_dump($currect_node_id,$this->tree_childs($currect_node_id));
      //   echo '<hr>';   
  
    //
    if(!($temp_continue_i < $source_size))
     {
        // continue;
     }  
        
   if($node_is_true == 0)
    {  
        //var_dump($node);//exit;
              $this->delete_childs_by_part($node_id,$currect_part);
            //  unset($this->tree[$node_id]);
            
              $temp_continue_i=$continue_i;    
              $col2_index=0;
              $col1_index++;
              
        
    }else
    {
        
         for($child_i=0;$child_i<count($node_childs);$child_i++)
         {
          if($node_childs[$child_i]['true'] == 0)
          {
              $this->delete_childs($node_id);
             // $this->tree[$node_id]['true']=0;
            
    
              $col1_index++;
              break;
          } 
         } 
    }
    
       
    
    
    
          if($node_type != 'var' ){ continue; }
    
     
      // var_dump($currect_data); 
     
     
     
   //  var_dump($currect_var,$col1_index,$col2_index);         echo '<hr>';
     if($counter >= $limit_counter){
         echo '<div style="font-size:30px;color:red;">end without response ; counter '.$counter. '</div>';
         var_dump($this->tree);
         exit;
     }
     
        
     while( isset($node_G_data[$col1_index]))
     {
         if($node_var=='exp')
         {
          //   var_dump($node_var.' '.$col1_index.' '.$col2_index) ;
         }
         
         $match_strings=0;
         while(  isset($node_G_data[$col1_index][$col2_index]))
        {
           // var_dump($node_var);
            
             if( ! (1) )
             {
                $this->delete_childs_by_part($node_id,$col1_index); 
                break;
             }
            
            
            
           $match_strings=0;       
           $item=$node_G_data[$col1_index][$col2_index];
          //  var_dump($item);
           if($item['type'] == 'string')
           {
             $str1=$item['value'] ;  
             $str2=substr($source,$temp_continue_i,strlen($item['value']));  
               
              if(strlen($str1)==strlen($str2) && $item['value'] == substr($source,$temp_continue_i,strlen($item['value'])))
              {
                         
                    var_dump('yes # '.$node_var.'# p = '.$col1_index.' # i-t ='.$temp_continue_i.' # '.$str1.'=='.$str2.'@end='.$source_size) ;  
                 
                 
                  $temp_continue_i += strlen($str1) ; 
                  $match_strings=1;
                         
                     
                    
                             
                              //  die('end . accept input') ;
                      //  $this->tree[$node_id]['match'] .= $str1;     
                      //   $this->tree[$node_id]['true']   =  1 ;     
                            
                              
                              
                              
                              $temp_id=$this->next_tree_id();
     $this->tree[$temp_id]= array('id'=>$temp_id,'part'=>$col1_index,'type'=>'string','value'=>'','match'=>$str1 , 'c1'=>$col1_index, 'c2'=>$col2_index ,'i'=>$continue_i , 'i-temp'=>$temp_continue_i ,'true'=>1 ,'parentid'=> $node_id);
 
                           
                 
                             
                            // $this->tree[$node_parentid]['i-temp'] = $temp_continue_i;
                             
                             
                             
                             
                                                     
                              
                      
                                
                   
                     if( ! isset($node_G_data[$col1_index][$col2_index+1]))  // not continue in piece 
                    {
                        $this->tree[$node_id]['true']   =   1;                           
                       if( $node_parentid != -1)
                       {
                             
                            // $this->tree[$node_parentid]['i-temp'] = $temp_continue_i;
                             
                             
                             $this->tree[$node_parentid]['true']   =   1; 
                             
                                                     
                              
                      
                       }
                      
                      
                      
                       break(1);
                  }
             
                  
                     
              }
              else
              {    
                
                  $this->delete_childs_by_part($node_id,$col1_index);
                  
                   if(!isset($node_G_data[$col1_index+1]) && !($this->tree_childs($node_id)) )
                   {    
                    //   $this->tree[$node_id]['true'] = 0;
                    //   $this->delete_childs_by_part($node_parentid,$currect_part);
                    //   unset($this->tree[$node_id]);
                    //  var_dump($node);exit;
                   }
                  var_dump('not # '.$node_var.'# p = '.$col1_index.' # temp-i= '.$temp_continue_i.' # '.$str1.' == '.$str2) ;
                  //var_dump($col1_index,$col2_index);
                  break;
              } 
               
               
           }
           elseif($item['type'] == 'var')
           {
              if( isset ($G[$item['value']]) )
              {
                     if($item['value']=='exp')
                     {
                         
                   // var_dump($node_var,$item['value'],$temp_continue_i);
                     }  
                    $flag_rec=0; 
                  if($col2_index==0 && $item['value'] == $node_var)
                  {
                      $temp_pid=$node_id; 
                     
                  
                      
                    while($temp_pid >=  0 )
                    {        
                         
                         
                         
                        $temp_node=$this->tree[$temp_pid];
                        
                       
                       if($temp_node['type']=='var' && $temp_node['value'] == $node_var)
                       {
                           if(isset($temp_node['flag_rec']) && $temp_node['flag_rec']==1)
                           {
                               
                              break(2); 
                           }
                          break;  
                          
                       }   
                       
                       
                       
                       $temp_pid=$temp_node['parentid'] ;
                     }  
                      $flag_rec=1; 
                  }                     
                    
                  $this->tree[$node_id]['i']      = $continue_i;               // update 
                  $this->tree[$node_id]['i-temp'] = $temp_continue_i;         // update
                 
                 
                  $stack  []= array('id'=> $node_id   , 'c1'=> $col1_index  ,'c2'=>$col2_index+1 )  ;
                      
                  //  var_dump($item['value'],$temp_continue_i);  
                                   
                    $temp_id=$this->next_tree_id();
                    $this->tree[$temp_id]= array('id'=>$temp_id,'flag_rec'=>$flag_rec,'part'=>$col1_index,'type'=>'var','value'=>$item['value'],'match'=>'', 'i'=>$temp_continue_i , 'i-temp'=>$temp_continue_i ,'true'=>-1 ,'parentid'=> $node_id);
 
               
                  $stack  []= array('id'=>$temp_id , 'c1'=>0  ,'c2'=>0  )  ; 
                  
                  
                   continue (3);   
               //  goto befor_stack; 
                  
               //   break(2);  
                  
              }else{
                  die('parse error . undefined var "'.$item['value'].'" ');
              } 
               
               
           }
           
        
         
           $col2_index++; 
       } // end while
       
         if(!$match_strings && isset($node_G_data[$col1_index][$col2_index]))
           {   //var_dump($this->tree);exit;
               //$this->delete_childs($node_id);
                 $this->delete_childs_by_part($node_id,$col1_index);
                 if($node_parentid != -1)
                 {
                    //$this->tree[$node_parentid]['true']=0;  
                 }
               
           }
          
           
            
       
         
             
       
       
          $col2_index=0;
          if(isset($node_G_data[$col1_index+1]))
          {
                    
              
              
              
              $temp_continue_i =$continue_i;
          }else
          {
              /*
               if($node_childs=$this->tree_childs($node_id))
               {
                   var_dump($node_childs);exit;    
               
                    $parts=array();
                for($child_i=0;$child_i<count($node_childs);$child_i++)
                {
             
                     //$this->tree[$node_id]['match'] .= $node_childs[$child_i]['match'];
                   //  var_dump($node_childs[$child_i]['match'],$node_childs[$child_i]['part']) ;
                   if(isset($parts[$node_childs[$child_i]['part']]['match']))
                   {
                       
                      
                      $parts[$node_childs[$child_i]['part']]['match']   .= $node_childs[$child_i]['match']; 
                   }else
                   {
                      $parts[$node_childs[$child_i]['part']]['id']       = $node_childs[$child_i]['id'] ;
                      $parts[$node_childs[$child_i]['part']]['parentid'] = $node_childs[$child_i]['parentid'] ;
                      $parts[$node_childs[$child_i]['part']]['match']    = $node_childs[$child_i]['match']; 
                   }
                     
                  }  
          // var_dump($parts) ; exit;
             $best_match='';
             $best_match_part_key=-1;
             $best_match_node_id=-1;
             $best_match_node_parentid=-1;
             
             foreach($parts as $part_key=>$part_value)
             {
                if($best_match_part_key == -1)
                {
                    $best_match=$part_value['match'] ;
                    $best_match_part_key=$part_key;
                    $best_match_node_id=$part_value['id'];
                    $best_match_node_parentid=$part_value['parentid'];
                }elseif(strlen($part_value['match']) > strlen($best_match))
                {
                    $best_match=$part_value['match'] ;
                    $best_match_part_key=$part_key;
                    $best_match_node_id=$part_value['id'];
                    $best_match_node_parentid=$part_value['parentid'];
                } 
             }
           //   var_dump($best_match,$best_match_part_key);
              
                if(empty($parts) )
             {
                   
                 $this->tree[$node_parentid]['true']   = 0;  
                 $this->delete_childs($node_id);
                 unset($this->tree[$node_id]);
                 continue;
             }
              
              
            for($child_i=0;$child_i<count($node_childs);$child_i++)
                {
                   if($node_childs[$child_i]['part'] != $best_match_part_key)
                   {
                       $this->delete_childs($node_childs[$child_i]['id']) ;
                       unset($this->tree[$node_childs[$child_i]['id']]);
                   }
                 
                     
                 }
            
              $this->tree[$best_match_node_id]['i-temp']       = strlen($best_match);
              $this->tree[$best_match_node_parentid]['i-temp'] = strlen($best_match);
                 
              $this->tree[$node_id]['match'] = $best_match;     
            // exit;
              //var_dump($this->tree[$node_id]['match']);
              
               //var_dump($this->tree[$best_match_node_id],$this->tree[$best_match_node_parentid]);;
          
                   
                   
               }else
               {
                   
                   
                 $this->tree[$node_parentid]['true']   = 0;  
                 $this->delete_childs($node_id);
                 unset($this->tree[$node_id]);
                   
               }
               
            
              */ 
               
          }
          
          
          
          
           if(!$match_strings)
           {
               //   array_pop($stack);
           }
         
        $col1_index++; 
     }
     
     
     
     
   if(  $this->tree[$node_id]['true'] )
    {  
            //  var_dump($node_var,$this->tree_childs($node_id));
             //   var_dump($node_var,$this->tree);
          if($node_childs=$this->tree_childs($node_id))
               {
                    
               
                    $parts=array();
                for($child_i=0;$child_i<count($node_childs);$child_i++)
                {
             
                     //$this->tree[$node_id]['match'] .= $node_childs[$child_i]['match'];
                   //  var_dump($node_childs[$child_i]['match'],$node_childs[$child_i]['part']) ;
                   if(isset($parts[$node_childs[$child_i]['part']]))
                   {
                       
                      
                      $parts[$node_childs[$child_i]['part']]['match']  .= $node_childs[$child_i]['match']; 
                      $parts[$node_childs[$child_i]['part']]['i-temp'] = $node_childs[$child_i]['i-temp']; 
                   }else
                   {
                      $parts[$node_childs[$child_i]['part']]['id']        = $node_childs[$child_i]['id'] ;
                      $parts[$node_childs[$child_i]['part']]['parentid']  = $node_childs[$child_i]['parentid'] ;
                      $parts[$node_childs[$child_i]['part']]['match']     = $node_childs[$child_i]['match']; 
                      $parts[$node_childs[$child_i]['part']]['i-temp']    = $node_childs[$child_i]['i-temp']; 
                   }
                     
                  }  
            // var_dump($parts,$node_childs) ; 
            //exit;
             $best_match='';
             $best_match_part_key=-1;
             $best_match_node_id=-1;
             $best_match_node_parentid=-1;
             $best_match_node_i_temp=-1;
             
             foreach($parts as $part_key=>$part_value)
             {
                if($best_match_part_key== -1)
                {
                    $best_match=$part_value['match'] ;
                    $best_match_part_key=$part_key;
                    $best_match_node_id=$part_value['id'];
                    $best_match_node_parentid=$part_value['parentid'];
                    $best_match_node_i_temp=$part_value['i-temp'];
                    
                }elseif(strlen($part_value['match']) > strlen($best_match))
                {
                    $best_match=$part_value['match'] ;
                    $best_match_part_key=$part_key;
                    $best_match_node_id=$part_value['id'];
                    $best_match_node_parentid=$part_value['parentid'];
                    $best_match_node_i_temp=$part_value['i-temp'];
                } 
             }
            //  var_dump($best_match_part_key,$best_match);
              
             if($best_match_part_key == -1 || empty($parts))
             {
                       
                  if($node_parentid != -1)
                  {
                      $this->tree[$node_parentid]['true']   = 0;
                  } 
                   
                 $this->delete_childs($node_id);
                 unset($this->tree[$node_id]);
                 continue;
             } 
              
              
              
            for($child_i=0;$child_i<count($node_childs);$child_i++)
                {
                   if($node_childs[$child_i]['part'] != $best_match_part_key)
                   {
                       $this->delete_childs($node_childs[$child_i]['id']) ;
                       unset($this->tree[$node_childs[$child_i]['id']]);
                   }
                 
                     
                 }            
            
            if($node_var=='start'){
           //var_dump($best_match_node_i_temp);
            }
            
            
            //  $this->tree[$best_match_node_id]['i-temp']        += strlen($best_match);
             // $this->tree[$best_match_node_parentid]['i-temp']  = $this->tree[$best_match_node_id]['i-temp']+strlen($best_match);
              $this->tree[$best_match_node_parentid]['i-temp']  = $best_match_node_i_temp;
             // $this->tree[$best_match_node_parentid]['i-temp']  = $this->temp_continue_i($node_id,$currect_part);
              $this->tree[$best_match_node_parentid]['true']    =  1;
              $this->tree[$best_match_node_id]['true']          =  1;
              $this->tree[$node_id]['match']                    = $best_match;  
            // exit;
             
          
                
                   
               }else
               {
                    
                 if($node_parentid != -1)
                 {
                     $this->tree[$node_parentid]['true']   = 0;
                 }  
                   
                 $this->delete_childs($node_id);
                // unset($this->tree[$node_id]);
                 continue;
                   
               }
        
            //var_dump($this->tree);
        
  /*      
      if(!isset($node_G_data[$col1_index][$col2_index]))
      {
        
          if($node_parentid != -1 )
          {  
              
              $this->tree[$node_parentid]['i-temp'] = $temp_continue_i; 
              $this->tree[$node_parentid]['true']   = 1;
          
          
           
              
           
           }    
        
          continue;   
       }   
       */
          
    } 
    else
    {
                 $this->delete_childs_by_part($node_id,$col1_index);
                 unset($this->tree[$node_id]);
       // var_dump($this->tree) ;
        
    }  

     
 }// end while !empty stack
 
 
     $final_temp_continue_i= isset($this->tree[0]['i-temp'])? $this->tree[0]['i-temp'] : -1  ;
 
 
 
   if(isset($this->tree[0]) && $this->tree[0]['true']==1 && $final_temp_continue_i == $source_size)
   {                                          
       print('<div style="color:blue;font-size:30px;">yes accept input string.</div>') ;
   }
   
   
    foreach($this->tree as $tree_key=>$tree_value)
    {
        if($this->tree[$tree_key]['true'] == 0)
        {
           // unset($this->tree[$tree_key]);
        }
        elseif($this->tree[$tree_key]['true'] == -1 && !isset($this->tree[$tree_value['parentid']]) && $tree_key!=0)
        {
           // unset($this->tree[$tree_key]);
        }
    }
 
   var_dump('end while.',$stack,$this->tree,'source-size => '.$source_size.' & currect-index => '.$final_temp_continue_i.' & counter => '.$counter);
    
      
    
    
} // end func
 }  
  
  
  
  
  
  
$rule=<<<eos
   start : num |  num start;
   num   : '0'|'1'|'2'|'3'|'4|'5'|'6'|'7|'8'|'9'  ;
   
          
eos;
         


         

$rule=<<<eos
   
   start :   num '.'  num  ;
   num   :  num-digit num  |  num-digit   ;
   num-digit   : '0'|'1'|'2'|'3'|'4'|'5'|'6'|'7'|'8'|'9'  ;
   
          
eos;

$rule=<<<eos
   
   start :    global;
   global:    define;
   define:    space-star '#define' space-plus id space-plus exp space-star ';';
   
   
   id    :    id-first-char id-continue ;
   id-continue:  id-char id-continue|id-char;
   id-first-char: '_'|'a'|'b';
   id-char:  '0'|'1'|'2'|'3'|'4'|'5'|'6'|'7'|'8'|'9'|'_'|'a'|'b';
   
   space-plus  :   ' ' space-plus | ' ' ;
   space-star  :   '' |' ' space-star | ' ';
   
   
   exp   :  num | '(' num ')'  | exp '+' exp;
   num   :  num-digit num  |  num-digit   ;
   num-digit   : '0'|'1'|'2'|'3'|'4'|'5'|'6'|'7'|'8'|'9'  ;
   
          
eos;


$rule=<<<eos
   
   start :  num  space-star num;
   space-star  :   '' |' ' space-star | ' ';
   num   :  num-digit num  |  num-digit   ;
   num-digit   : '0'|'1'|'2'|'3'|'4'|'5'|'6'|'7'|'8'|'9'  ;
          
eos;


   $str="12 23";
   
$rule=<<<eos
   
   start :   exp ;
   exp   :  num | '(' exp ')' |exp '+' exp;
   num   :  num-digit num  |  num-digit   ;
   num-digit   : "0"|'1'|'2'|'3'|'4'|'5'|'6'|'7'|'8'|'9'  ;
   
          
eos;



$str='(0+1)';
   

          
          
          
var_dump($rule);echo '<hr>' ;
var_dump($str);echo '<hr>' ;
$g=new bnf();
$r=$g->parse($rule,$str);
