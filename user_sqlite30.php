<?php

# == работа с sql разделом users sqlite 3.0 ====
# == версия 1.00 / 10.11.2018 (13.11.2018)  ====

# == index.php   ==

    function switch_view_sql30($guid, $db){
            $sql			= "SELECT * FROM company WHERE guid ='".$guid."';";
            $result_user	= $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

            if ($result_user[0][active]=="on"){
                $active="";
            }else{
                $active="on";
            }
            $db->exec("UPDATE company SET active ='".$active."' WHERE guid = '".$guid."';");
    }

    function del_company_sql30($guid, $db){
            $db->exec("DELETE FROM company WHERE guid = '".$guid."';");
    }

    function move_company_sql30($myid, $db, $side){
    
           $sql			= "SELECT * FROM company WHERE guid = '".$myid."' ORDER BY id DESC;";
           $res_user	= $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);    

           $id      = $res_user[0][id];    
           $msgid   = $res_user[0][msgid];
           $active  = $res_user[0][active];
           $guid    = $res_user[0][guid];
           $login   = $res_user[0][login];
           $naimen  = $res_user[0][naimen];
           $gorod   = $res_user[0][gorod];
           $phone   = $res_user[0][phone];
           $pass    = $res_user[0][pass]; 


           $sql_all         = "SELECT * FROM company ORDER BY id DESC;";   
           $res_user_all	= $db->query($sql_all)->fetchAll(PDO::FETCH_ASSOC);    
           $allzap          = count($res_user_all);    

           //echo "тек ид ".$id."<br>";

           for($ik=0;$ik<$allzap;$ik++){
               if ($res_user_all[$ik][guid]==$guid){
                   $tmp_side = $res_user_all[$ik+$side][id];
                   continue;
               }
           }    

           //echo "куда перем ".$tmp_side;    

           $sql_all_otkuda        = "SELECT * FROM company WHERE id= '".$tmp_side."' ORDER BY id DESC;";   
           $res_user_otkuda	      = $db->query($sql_all_otkuda)->fetchAll(PDO::FETCH_ASSOC);     

           //exit();

           // из 3 мы запишем в 1-ый

           $db->exec("UPDATE company SET 
                          msgid     = '".$res_user_otkuda[0][msgid]."', 
                          active    = '".$res_user_otkuda[0][active]."',
                          guid      = '".$res_user_otkuda[0][guid]."',
                          login     = '".$res_user_otkuda[0][login]."', 
                          naimen    = '".$res_user_otkuda[0][naimen]."', 
                          gorod     = '".$res_user_otkuda[0][gorod]."', 
                          phone     = '".$res_user_otkuda[0][phone]."', 
                          pass      = '".$res_user_otkuda[0][pass]."' 
                      WHERE 
                          id = '".$id."';");    

           // из ТМР мы запишем в 3-ий    

           $db->exec("UPDATE company SET 
                          msgid     = '".$msgid."', 
                          active    = '".$active."',
                          guid      = '".$guid."',
                          login     = '".$login."', 
                          naimen    = '".$naimen."', 
                          gorod     = '".$gorod."', 
                          phone     = '".$phone."', 
                          pass      = '".$pass."' 
                      WHERE 
                          id = '".$tmp_side."';");     
    
    }


# == adduser.php ==

    function save_sql($blocks_sql, $db){
                
            $sql			= "SELECT * FROM company WHERE guid='".$blocks_sql[guid]."';";
            $result_user	= $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
            $allusers		= count($result_user); 
        
            if ($allusers==0){
            
                        $db->exec('INSERT INTO company (   msgid, 
                                                           active, 
                                                           guid,
                                                           login, 
                                                           naimen, 
                                                           gorod, 
                                                           phone, 
                                                           pass
                                                        
                                                        ) VALUES (
                                                        
                                                           "'.$blocks_sql[msgid].'",
                                                           "'.$blocks_sql[active].'",
                                                           "'.$blocks_sql[guid].'",
                                                           "'.$blocks_sql[login].'",
                                                           "'.$blocks_sql[naimen].'",
                                                           "'.$blocks_sql[gorod].'",
                                                           "'.$blocks_sql[phone].'",
                                                           "'.$blocks_sql[pass].'");'
                         );
           
            }else{
               
                        $db->exec("UPDATE company SET 
                                    msgid   ='".$blocks_sql[msgid]."', 
                                    active  ='".$blocks_sql[active]."', 
                                    login   = '".$blocks_sql[login]."', 
                                    naimen  ='".$blocks_sql[naimen]."', 
                                    gorod   = '".$blocks_sql[gorod]."', 
                                    phone   ='".$blocks_sql[phone]."', 
                                    pass    = '".$blocks_sql[pass]."' 
                                WHERE 
                                    guid = '".$blocks_sql[guid]."';");
                
            }
        
		allclose();
		exit();       
        
    }

    function read_sql($db,$idread){
        
        $sql			  = "SELECT * FROM company WHERE guid='".$idread."';";
        $result_user	  = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        return array("msgid"    => $result_user[0][msgid],
                     "active"   => trim($result_user[0][active]),
                     "guid"     => $result_user[0][guid],
                     "login"    => $result_user[0][login],
                     "naimen"   => $result_user[0][naimen],
                     "gorod"    => $result_user[0][gorod],
                     "phone"    => $result_user[0][phone],
                     "pass"     => $result_user[0][pass]);
    }


# == технические функции ==

    function readtxtdatesimple($pathtodate){
        $data_rub           = file($pathtodate);
        $mainarea_rub       = trim($data_rub[0]);
        $mainarea_rub       = stripslashes($mainarea_rub);
        $txtdate            = split(",", $mainarea_rub);
        return $txtdate;	
    }

    function getGUID(){
            mt_srand((double)microtime()*10000);
            $charid = strtoupper(md5(uniqid(rand(), true)));
            $hyphen = chr(45);
            $uuid = substr($charid, 0, 8).$hyphen
            .substr($charid, 8, 4).$hyphen
            .substr($charid,12, 4).$hyphen
            .substr($charid,16, 4).$hyphen
            .substr($charid,20,12);
            return $uuid;
        }

    function allclose(){
            echo"<html>";
            echo"<head><title>Добавление...</title>";
            echo"<script languare='javascript'>";
            echo"top.opener.location.href = 'index.php'";
            echo"</script>";
            echo"</head>";
            echo"<body></body></html>";
            echo"<script languare='javascript'>";
            echo"window.close()";
            echo"</script>";
            exit();
        }







?>