<?php
set_time_limit(3);
class bnf{
    
       private  $delimiter =':';   
       
       private  $parse_start_var ='start'; 
       private  $next_tree_id=0;
         
    
       private  $tree=array();
       private  $grammer=array();
            
       private  $debug_mode=0;
       private  $debug_limit_counter=100;
             
public   function  __construct(){
        
}     
private  function  grammer_start_protocol($str){
    
    return ctype_alnum($str) || $str=='-' || $str=='_';
    
} 
private  function  strip_space_comment($str='',$size=0,$i=0,&$line_count=0){ 
    $line_count=0;
    
    
    
    
   
    
  $is_exist_comment=1;
  while($i<$size && $is_exist_comment)
  {  
      
     $is_exist_comment=0;
     
     
      
      while($i<$size&&ord($str[$i])<=32)   // strip space
   {
       if(ord($str[$i])==10)
       {
           $line_count++;
       }
       $i++;
   }
      
      
      
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
      elseif($str[$i]=='[')
      {   
          
          $flag_true_ended_braked=0;
          $i++;
          $arr_braket_piece=array();
          while($i<$strlen)
          {
            
              $i = $this->strip_space_comment($str,$strlen,$i,$t_line) ;  // is space , newline , tab , ....
              $line += $t_line;  
             
              if($str[$i]=='-')
              {
                   
              }
              elseif($str[$i]==']')
              {
                 $flag_true_ended_braked=1; 
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
            
                  }elseif(strlen($t_piece)%2==1)
                  {
                      die('error on line '.$line.' . hex len is not odd  '); 
                  }
         
                    $i = $this->strip_space_comment($str,$strlen,$i,$t_line) ;  // is space , newline , tab , ....
                    $line += $t_line;
             
                    if($str[$i] == '.' && ($i+1) < $strlen &&$str[$i+1] == '.'  )
                    {
                      $i+=2;  
                      $t_piece2='';
              
                      $i = $this->strip_space_comment($str,$strlen,$i,$t_line) ;  // is space , newline , tab , ....
                      $line += $t_line;

                      
                    if($str[$i] == '\\' && ($i+1)<$strlen &&($str[$i+1] == 'U'  ||  $str[$i+1] == 'u'))
                    {
                       $i+=2;
          
                           while($i<$strlen && $str[$i]>='0'&&$str[$i]<='9' || $str[$i]>='a'&&$str[$i]<='f' || $str[$i]>='A'&&$str[$i]<='F') 
                          {
                             $t_piece2 .= $str[$i]; 
                             $i++;
                           } $i--;
         
       
                      }
          
                    if(!strlen($t_piece2)  )
                    {
                         die('error on line '.$line.' unexpected  token ".." "\u" '); 
                   
                    }elseif( strlen($t_piece2) != strlen($t_piece))
                    {
                         die('error on line '.$line.' . hex len1 != hex len2   '); 
                    } 
             
             
                       $arr_braket_piece  []= array('type'=>'range' ,'from'=>$t_piece ,'to' => $t_piece2);
              
                        
             
                    }else
                    {
                       $arr_braket_piece  []= array('type'=>'char' , 'value' => $t_piece);
                       $i--;
                    }
                        
               }
              else
              {
                 die('error on line '.$line.' unexpected  token "'.$str[$i].'"');   
              }
              
              
              $i++;   
          }
          if(!$flag_true_ended_braked)
          {
            die('error on line '.$line.' unexpected  token "]" ');   
          }
          $array_piece [$piece_counter][]= array('type'=>'chars' , 'value'=>$arr_braket_piece);
      }
      elseif(in_array($str[$i],array('+','*','?')))
      {
         
         
         if(!empty($array_piece[$piece_counter]))
         {
             
             $temp_pp=array_pop($array_piece[$piece_counter]);
             
             if(in_array($temp_pp['type'],array('chars')))
             {
                $temp_pp['op'] = $str[$i]; 
                $array_piece[$piece_counter][]=$temp_pp; 
             }else
             {
               die('error on line '.$line.' unexpected  token "'.$str[$i].'" ');   
             }
         }else
         {
             die('error on line '.$line.' unexpected  token "'.$str[$i].'" ');
         }
         
         //var_dump($array_piece,$piece_counter);exit; 
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
return ($grammer);
  }  // end func parser
private  function  next_tree_id(){
    return $this->next_tree_id++;
}
public   function  tree_childs($id=0){
    
   $childs=array();
  foreach($this->tree as $tree_key=>$tree_value)
  {
      
     if($tree_value['pid'] == $id)
     {
         $childs []= $tree_value;
     } 
      
  } 
   
   
   
   
  
   return $childs;
   
   
}
private  function  tree_childs_by_part($id=0,$part=0){
    
   $childs=array();
  foreach($this->tree as $tree_key=>$tree_value)
  {
      
     if($tree_value['pid'] == $id && $tree_value['p'] == $part)
     {
         $childs []= $tree_value;
     } 
      
  } 
   
   
   
   
  
   return $childs;
   
   
}
private  function  delete_childs($id=0){
    
   foreach($this->tree as $tree_key=>$tree_value)
  {  
      
     if($tree_value['pid'] == $id)
     {
         $this->delete_childs($tree_value['id']);
         unset($this->tree[$tree_key]);
     } 
      
  } 
   
   
   return 1;
   
   
}
private  function  delete_childs_by_part($id=0,$part=0){
    
   foreach($this->tree as $tree_key=>$tree_value)
  {  
      
     if($tree_value['pid'] == $id && $tree_value['p'] == $part)
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
      
    
         $find = $tree_value['i-tmp'];
      
  } 
   
   if($find != -1)
   {
       return $find;
   } 
   return $this->tree[$id]['i'];
   
   
}
private  function  hex2string($hex_string=''){
    
    $string='';
  
    for($i=0,$size=strlen($hex_string);$i<$size;$i+=2)
    {
       $string .= chr(hexdec(substr($hex_string,$i,2))); 
    }
    return $string;
   
}

private  function  tree_copy($node_id='',$parent_id=-1,$copy_part=-1,$new_part=-1,$copy_part2=-1){
  
    if(isset($this->tree[$node_id]))
    {
        if($copy_part==-1)
        {
          $part_childs=$this->tree_childs($node_id);  
        }else
        {
           $part_childs=$this->tree_childs_by_part($node_id,$copy_part);  
        }
       
       
       
       
       $c=0; 
       foreach($part_childs as $key=>$value)
       {  
           
           
           if($c==$copy_part2)
           {
               break;
           }
        
          $temp_id=$value['id'];
          $temp_part=$value['p'];
        
          $new_id=$this->next_tree_id();  
          $value['id']=$new_id ;
          
           if($copy_part != -1)
           {
             $value['p']=$new_part ;  
           }
           if($parent_id >=0)
           {
               $value['pid']=$parent_id ;
           }
        
          
          $this->tree[$new_id]=$value;
         if($value['type']=='var')
         {
             $this->tree_copy($temp_id,$new_id,-1,-1,-1);
         } 
          
          $c++;
       } 
    }
    
}
public   function  parse($rule='',$source='',&$error=0){

$G=$this->grammer=$this->bnf_rule_parser($rule);
$parse_tree=array();
$stack=array();
$source_size= strlen($source);
$temp_continue_i=0;

$tree_part_after_delete=array();



$this->tree = array();


if(! isset($G[$this->parse_start_var]))
{
die ('error parse . not exist start var "'.$this->parse_start_var.'" .');
}
$first_node_id=$this->next_tree_id();


$stack[]=array('id'=>$first_node_id ,'c1'=>0, 'c2'=>0);

$this->tree[$first_node_id]= array(
                            'id'=>$first_node_id,
                            'p'=>0 ,
                            'type'=>'var',
                            'value'=>$this->parse_start_var,
                            'match'=>''  ,
                            'i'=>0 ,
                            'i-tmp'=>0 ,
                            'true'=>-1 ,
                            'pid'=> -1
                           );

$counter=0;






while(!empty($stack))
{        

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
$node_pid  = $node['pid']; 


$col1_index     = $pop_stack['c1'];
$col2_index     = $pop_stack['c2'];
$currect_part   = $col1_index;

    
$continue_i      =  $node['i'] ; 
$temp_continue_i =  $this->temp_continue_i($node_id,$currect_part);


if(!($temp_continue_i < $source_size))
{
// continue;
}  

if($node_is_true == 0)
{              
$this->tree[$node_id]['true'] = -1 ;
      $this->delete_childs_by_part($node_id,$currect_part);
    //  unset($this->tree[$node_id]);
    
      $temp_continue_i=$continue_i;    
      $col2_index=0;
      $col1_index++;
      

}





  if($node_type != 'var' ){ continue; }




if($this->debug_mode && $counter >= $this->debug_limit_counter){
 echo '<div style="font-size:30px;color:red;">end without response ; counter '.$counter. '</div>';
 var_dump($this->tree);
 exit;
}


while( isset($node_G_data[$col1_index]))
{

if($col1_index > 0 && $col2_index == 0)
  {   
      $similar_terminal=array();
      
      for($t1=0;$t1<$col1_index;$t1++)
      {
          $similar_to = 0 ;
          $tc= $this->tree_childs_by_part($node_id,$t1);    
          for($t2=0 ; $t2<count($tc) && $t2< count($node_G_data[$col1_index]) ; $t2++)
          {    
              if($node_G_data[$col1_index][$t2] == $node_G_data[$t1][$t2])
              {
                 $similar_to++;
              }
          }
          if($similar_to)
          {
             $similar_terminal []= array('p'=>$t1,"count"=>$similar_to) ;
          }
         
      }
      if($similar_terminal)
      {
          $best_similar_part=-1;
          $best_similar_part_count=-1;
          
          
         for($t=0;$t<count($similar_terminal);$t++)
         {
            if(count($this->tree_childs_by_part($node_id,$similar_terminal[$t]['p'])))
            {
                 if($similar_terminal[$t]['count'] > $best_similar_part_count )
                {
                   $best_similar_part_count = $similar_terminal[$t]['count'] ;
                   $best_similar_part       = $similar_terminal[$t]['p']  ;
                }
            }
            
         }
         
         
         
         $this->tree_copy($node_id,-1,$best_similar_part,$col1_index,$best_similar_part_count);
         $temp_continue_i =  $this->temp_continue_i($node_id,$col1_index);
         $col2_index = $best_similar_part_count  ;
         
         if(isset($tree_part_after_delete[$node_id]))
         {       
               for($t=0;$t<count($tree_part_after_delete[$node_id]);$t++)
               { 
                   if(isset($tree_part_after_delete[$node_id][$t]))
                   {
                      if($tree_part_after_delete[$node_id][$t] == $best_similar_part)
                       {
                          $tree_part_after_delete[$node_id][]= $col1_index; 
                       }
                       
                   }
   
               }

         }
         
         
      }
     
      
  }


 
 $match_strings=0;
 while(  isset($node_G_data[$col1_index][$col2_index]))
{
  
 
    
    
    
   $match_strings=0;       
   $item=$node_G_data[$col1_index][$col2_index];
   if($item['type'] == 'string' || $item['type'] == 'chars' )   
   {    
       
       
       
       $ltrim_str2_i=0;
       if($item['type'] == 'chars')
       {
           $item_op=isset($item['op'])?$item['op']:''; // + * ?
           $item_value=$item['value'];
           $flag_repeat=1;
           $repeat_counter=0;
           $repat_match='';
        while($flag_repeat)
        {
            
           
           for($j=0;$j<count($item_value);$j++)
           {
             if($item_value[$j]['type'] == 'char')
             {
                 $str1=$this->hex2string($item_value[$j]['value']) ;  //  example :  \u61   is char 'a'
                 $str2=substr($source,$temp_continue_i,strlen($str1)); 
                 
                 $match_strings = strlen($str1)==strlen($str2) && $str1==$str2 ; 
                 
                 
                 break;
             }
             elseif($item_value[$j]['type'] == 'range')
             {   $str1=$str2=''; 
                 $temp_continue_i_plussed=$temp_continue_i;
               for($k=0;$k<strlen($item_value[$j]['from']);$k+=2) 
               {
                  $str_from = substr($item_value[$j]['from'],$k,2);
                  $str_to   = substr($item_value[$j]['to'],$k,2);
                  
          
                  if($temp_continue_i_plussed>=$source_size)
                  {
                      $match_strings=0;
                      break;
                  } 
                   $ord_char_from = hexdec($str_from);
                   $ord_char_to   = hexdec($str_to);
              
                    if($ord_char_from > $ord_char_to)
                   {
                        $temp_ch       =$ord_char_from ; 
                        $ord_char_from =$ord_char_to   ;
                        $ord_char_to   =$temp_ch       ;
                    }
              
                    $input_char=substr($source,$temp_continue_i_plussed,1); 
                    $ord_input_char=ord($input_char);
          
                   if($ord_input_char >= $ord_char_from  && $ord_input_char <= $ord_char_to)
                   {
                         
                         $str1 .= $input_char;
                         $str2 .= $input_char;
                         $match_strings=1;
                         $temp_continue_i_plussed++; 
                        // break;
                   }else
                   {
                       $match_strings=0;
                       break;
                   }   
                }
             
                if($match_strings)
                {
                   // $temp_continue_i = $temp_continue_i_plussed;
                    break;
                }
          
             }
             else
             {
                 die('error 21212') ;
             } 
             
          
           }
           
           
           
              if($match_strings)
             {
              
                $repat_match .= $str1 ; 
                $temp_continue_i += strlen($str1); 
                 
                if($item_op == '+')
                {
                   
                    
                }elseif($item_op == '*')
                {
                   
                    
                }elseif($item_op == '?')
                {
                    
                   break; 
                   
                }else
                {
                   break;  
                } 
             }
              else
             {
                if($item_op == '+')
                {
                   if($repeat_counter)
                   {
                       $match_strings=1;
                   }
                   
                    
                }elseif($item_op == '*')
                {
                  $match_strings=1;
                  
                  
                }elseif($item_op == '?')
                {   
                   $match_strings=1 ;
                   
                }
                
                break;
             }
             
                
          $repeat_counter++; 
        }   
           
          $str1 = $str2 =$repat_match ;  
       
           
       }
       elseif($item['type']=='string')   // is string
       {
           
           
     
          
           while(($temp_continue_i+$ltrim_str2_i) < $source_size && ord($source[$temp_continue_i+$ltrim_str2_i])<=32) // ltriming
          {
              
              $ltrim_str2_i++;
          }
          
           $str1=$item['value'];
           $str2=substr($source,$temp_continue_i+$ltrim_str2_i,strlen($str1));

           $match_strings = strlen($str1)==strlen($str2) && $str1==$str2 ;
       }
       else
       {
           die('error 123145');
       }

      if($match_strings)
      {
           if($this->debug_mode)
           {
               var_dump('yes # '.$node_var.'# p = '.$col1_index.' # i-t ='.$temp_continue_i.' # '.$str1.'=='.$str2.'@end='.$source_size) ;  
            
           }      
         
           if($item['type'] == 'string' )
           {
               $temp_continue_i += strlen($str1)+$ltrim_str2_i ;
           }
            
                 
             
            
        
                      
                      
                      $temp_id=$this->next_tree_id();
$this->tree[$temp_id]= array('id'=>$temp_id,'p'=>$col1_index,'type'=>'string','value'=>'','match'=>$str1 , 'c1'=>$col1_index, 'c2'=>$col2_index ,'i'=>$continue_i , 'i-tmp'=>$temp_continue_i ,'true'=>1 ,'pid'=> $node_id);

                   
         
             
                     
                                             
                      
              
                        
           
             if( ! isset($node_G_data[$col1_index][$col2_index+1]))  // not continue in piece 
            {
                $this->tree[$node_id]['true']   =   1;                           
               if( $node_pid != -1)
               {
                     
                    // $this->tree[$node_pid]['i-tmp'] = $temp_continue_i;
                     
                     
                     $this->tree[$node_pid]['true']   =   1; 
                     
                                             
                      
              
               }
              
              
              
               break(1);
          }
     
          
             
      }
      else
      {    
        
          //$this->delete_childs_by_part($node_id,$col1_index);
          $tree_part_after_delete[$node_id] []= $col1_index ;
          
         // $temp_id=$this->next_tree_id();
          
         // $this->tree[$temp_id]= array('id'=>$temp_id,'p'=>$col1_index,'type'=>'stop','value'=>$col2_index, 'i'=>$temp_continue_i , 'i-tmp'=>$temp_continue_i ,'true'=>-1 ,'pid'=> $node_id);

          
          
      
           if($this->debug_mode)
           {
              var_dump('not # '.$node_var.'# p = '.$col1_index.' # temp-i= '.$temp_continue_i.' # '.$str1.' == '.$str2) ;
           }
          break;
      } 
       
       
   }
   elseif($item['type'] == 'var')
   {
      if( isset ($G[$item['value']]) )
      {
            $flag_rec=0; 
            
          if($col2_index==0 )
          {     
              $temp_pid=$node_id; 
             
          
              
            while($temp_pid >=  0 )
            {        
               
                 
                 
                $temp_node=$this->tree[$temp_pid];
                
               
               if($temp_node['type']=='var' && $temp_node['value'] == $item['value'])
               {
                   
                   if(isset($temp_node['f_r']) && $temp_node['f_r']==1)  // flag_recursive
                   {
                         
                       break(2); 
                   }
                   
                   $flag_rec=1; 
                   break;  
                  
               }   
               
               
               
               $temp_pid=$temp_node['pid'] ;
             }  
                
          }                     
            
          $this->tree[$node_id]['i']      = $continue_i;        // update 
          $this->tree[$node_id]['i-tmp'] = $temp_continue_i;   // update
         
         
          $stack  []= array('id'=> $node_id   , 'c1'=> $col1_index  ,'c2'=>$col2_index+1 )  ;
       
                           
            $temp_id=$this->next_tree_id();
            $this->tree[$temp_id]= array('id'=>$temp_id,'f_r'=>$flag_rec,'p'=>$col1_index,'type'=>'var','value'=>$item['value'],'match'=>'', 'i'=>$temp_continue_i , 'i-tmp'=>$temp_continue_i ,'true'=>-1 ,'pid'=> $node_id);

       
          $stack  []= array('id'=>$temp_id , 'c1'=>0  ,'c2'=>0  )  ; 
           
          
           continue (3);   
          
          
      }else{
          die('parse error . undefined var "'.$item['value'].'" ');
      } 
       
       
   }
   

 
   $col2_index++; 
} // end while
   
        if(count($node_G_data[$col1_index])==count($this->tree_childs_by_part($node_id,$col1_index)))
        {
             if(isset($tree_part_after_delete[$node_id]))
             {
                 
               for($t=0;$t<count($tree_part_after_delete[$node_id]);$t++)
               { 
                  if(isset($tree_part_after_delete[$node_id][$t]))
                  {
                      if($tree_part_after_delete[$node_id][$t] == $col1_index)
                     {
                        unset($tree_part_after_delete[$node_id][$t]);
                     }
                       
                  }  
   
               }
             }
        }
   
      
     


  $col2_index=0;
  if(isset($node_G_data[$col1_index+1]))
  {
            
      
      $col1_index++; 
      
      $temp_continue_i =$continue_i;
  }else
  {
      break;
 
       
  }
  
  
  
  

 

}       


if(! isset($node_G_data[$col1_index]))
{
$col1_index--;  
} 



if(isset($tree_part_after_delete[$node_id]))
{         
for($t=0;$t<count($tree_part_after_delete[$node_id]);$t++)
{  
    if(isset($tree_part_after_delete[$node_id][$t]))
    {
       $this->delete_childs_by_part($node_id,$tree_part_after_delete[$node_id][$t]); 
    }
   
   
}

} 


   
if(  $this->tree[$node_id]['true'] )
{                
    
  if($node_childs=$this->tree_childs($node_id))
       {
          
            $parts=array();
        for($child_i=0;$child_i<count($node_childs);$child_i++)
        {
     
             
           if(isset($parts[$node_childs[$child_i]['p']]))
           {
               
              
              $parts[$node_childs[$child_i]['p']]['match']  .= $node_childs[$child_i]['match']; 
              $parts[$node_childs[$child_i]['p']]['i-tmp'] = $node_childs[$child_i]['i-tmp']; 
              
           }else
           {
              $parts[$node_childs[$child_i]['p']]['id']        = $node_childs[$child_i]['id'] ;
              $parts[$node_childs[$child_i]['p']]['pid']  = $node_childs[$child_i]['pid'] ;
              $parts[$node_childs[$child_i]['p']]['match']     = $node_childs[$child_i]['match']; 
              $parts[$node_childs[$child_i]['p']]['i-tmp']    = $node_childs[$child_i]['i-tmp']; 
           }
             
          }  
    
     $best_match='';
     $best_match_part_key=-1;
     $best_match_node_id=-1;
     $best_match_node_pid=-1;
     $best_match_node_i_temp=-1;
     
     foreach($parts as $part_key=>$part_value)
     {
        if($best_match_part_key== -1)
        {
            $best_match=$part_value['match'] ;
            $best_match_part_key=$part_key;
            $best_match_node_id=$part_value['id'];
            $best_match_node_pid=$part_value['pid'];
            $best_match_node_i_temp=$part_value['i-tmp'];
            
        }elseif(strlen($part_value['match']) > strlen($best_match))
        {
            $best_match=$part_value['match'] ;
            $best_match_part_key=$part_key;
            $best_match_node_id=$part_value['id'];
            $best_match_node_pid=$part_value['pid'];
            $best_match_node_i_temp=$part_value['i-tmp'];
        } 
     }       
      
     if($best_match_part_key == -1 || empty($parts))
     {
               
          if($node_pid != -1)
          {
              $this->tree[$node_pid]['true']   = 0;
          } 
           
         $this->delete_childs($node_id); 
         unset($this->tree[$node_id]);
         continue;
     } 
      
      
      
    for($child_i=0;$child_i<count($node_childs);$child_i++)
        {
           if($node_childs[$child_i]['p'] != $best_match_part_key)
           {   
               $this->delete_childs($node_childs[$child_i]['id']) ;
               unset($this->tree[$node_childs[$child_i]['id']]);
           }
         
             
         }            
    
   
      
    //  $this->tree[$best_match_node_id]['i-tmp']        += strlen($best_match);
     // $this->tree[$best_match_node_pid]['i-tmp']  = $this->tree[$best_match_node_id]['i-tmp']+strlen($best_match);
      $this->tree[$node_id]['i-tmp']  = $best_match_node_i_temp;
     // $this->tree[$best_match_node_pid]['i-tmp']  = $this->temp_continue_i($node_id,$currect_part);
     if($node_pid  !=  -1)
     {
         $this->tree[$node_pid]['true']    =  1;
     }
   
      
      
      
      
      
      $this->tree[$node_id]['true']    =  1;
      $this->tree[$best_match_node_id]['true']          =  1;
      $this->tree[$node_id]['match']                    = $best_match;  
   
                   
  
        
           
       }
       else
       {         // no child in node
           
           
           
           $this->delete_childs($node_id);
           $this->tree[$node_id]['true']=0 ;
           if($node_pid != -1)
           {
               $this->tree[$node_pid]['true']=0 ;
           }
           continue;

       }
  
   

  
} 
else
{           
         $this->delete_childs_by_part($node_id,$col1_index);
         unset($this->tree[$node_id]);
         
         if($node_pid != -1)
         {  
           // $this->tree[$node_pid]['true'] = 0;  
             
         }

}  
 

}// end while !empty stack


$final_temp_continue_i= isset($this->tree[0]['i-tmp'])? $this->tree[0]['i-tmp'] : -1  ;



if(isset($this->tree[0]) && $this->tree[0]['true']==1 && $final_temp_continue_i == $source_size)
{                                          
//print('<div style="color:blue;font-size:30px;">yes accept input string.</div>') ;
}else
{
$error=1;
}


foreach($this->tree as $tree_key=>$tree_value)
{
if($this->tree[$tree_key]['true'] == 0)
{
   // unset($this->tree[$tree_key]);
}
elseif($this->tree[$tree_key]['true'] == -1 && !isset($this->tree[$tree_value['pid']]) && $tree_key!=0)
{
   // unset($this->tree[$tree_key]);
}
}


return $this->tree;  

} // end func

 } // end class  
  
  
  
  
  
  
  
  
  
  
  
  
  
$rule=<<<eos
start : exp ;
exp : num | '(' exp ')' |exp '+' exp;
num : num-digit num | num-digit ;
num-digit : "0"|'1'|'2'|'3'|'4'|'5'|'6'|'7'|'8'|'9' ;
eos;

$str='(0+1)';

var_dump($rule);echo '<hr>' ;
var_dump($str);echo '<hr>' ;
$g=new bnf();
$r=$g->parse($rule,$str);
var_dump($r);
