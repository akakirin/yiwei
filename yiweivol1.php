<?php
/**
  * wechat php test
  */
date_default_timezone_set('prc');
//define your token
define("TOKEN", "wujunyi");
$wechatObj = new wechatCallbackapiTest();
//$wechatObj->valid();
$wechatObj->responseMsg();

class wechatCallbackapiTest
{
	public function valid()
    {
        $echoStr = $_GET["echostr"];

        //valid signature , option
        if($this->checkSignature()){
        	echo $echoStr;
        	exit;
        }
    }

    public function responseMsg()
    {
		//get post data, May be due to the different environments
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

      	//extract post data
		if (!empty($postStr)){
                
              	$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
                $fromUsername = $postObj->FromUserName;
                $toUsername = $postObj->ToUserName;
                $keyword = trim($postObj->Content);
				$type = trim($postObj->Event);
				$EventKey = $postObj->EventKey;
                $time = time();
                $textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>";             
				
				/* if($type=="CLICK" and $EventKey=="V1001_YIWEI_LIBRARY")
				{
					$msgType = "text";
					$contentStr = "<a href='http://1.yiweilibrary.applinzi.com/'>登陆</a>";
					$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                	echo $resultStr;
				}
				if($type=="CLICK" and $EventKey=="V1001_GOOD")
				{
					$msgType = "text";
                    $contentStr = "大家好，欢迎关注移动维护\n中心公众号，请点击链接进\n行还书、借书等操作哦~";
					$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                	echo $resultStr;
				} */
				
				$connection=mysqli_connect(SAE_MYSQL_HOST_M,SAE_MYSQL_USER,SAE_MYSQL_PASS,SAE_MYSQL_DB,SAE_MYSQL_PORT);
				
				if(!empty( $keyword ))
                {
              		if ($keyword=="图书馆" ){
						if ($fromUsername=="oMwWKjlU2bW9zFprLa7fJ-2MAmsQ" OR $fromUsername=="oo8emwIceaGfzsa_MryogrA2EBm4"){
						    $contentStr = "大家好，欢迎来到移动维护中心维图书馆~请输入以下数字进行相应操作:\n1：查询所有书籍编号列表\n2：查询剩余可借\n3：我要借书\n4：我要还书\n8：查看需在5日内归还的用户及已逾期的用户\n9；对当前待确认的归还行为进行确认操作\n0：回主菜单\n直接输入书籍编号可查看本书豆瓣图书链接、当前状态及在哪位亲的手中哦~";
					    }else{
							$contentStr = "大家好，欢迎来到移动维护中心维图书馆~请输入以下数字进行相应操作:\n1：查询所有书籍编号列表\n2：查询剩余可借\n3：我要借书\n4：我要还书\n0：回主菜单\n直接输入书籍编号可查看本书豆瓣图书链接、当前状态及在哪位亲的手中哦~";
						}
					}
					//查看所有书籍
					if  ($keyword=="1"){
						    //mysqli_select_db($connection,SAE_MYSQL_DB);										 
	                   // mysql_select_db(SAE_MYSQL_DB,$connection);
					    if ($connection){
	                        $sql1 = "SELECT book_id,book_name,douban_url FROM Books";
	                        $book_list=mysqli_query($connection,$sql1);
							$contentStr="所有书籍列表如下~\n开头冒号前如“b001”的编号即为书籍编号~\n\n";
							while($row_booklist=mysqli_fetch_array($book_list,MYSQLI_NUM)){
							    $contentStr.="$row_booklist[0]:\n$row_booklist[1]\n";
							}
						    mysqli_free_result($book_list);
							
							    //$contentStr.="{$row['book_id']}:<a href='{$row['douban_url']}'>{$row['book_name']}</a>".\n;
								//$contentStr=$contentStr."{$row['book_id']}:<a href='{$row['douban_url']}'>{$row['book_name']}</a>"."<br/>";
								//$contentStr="1111";
							//}
							//	$msgType = "text";
					         //   $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                	        //    echo $resultStr;
								
								//echo $contentStr;
							
						    //mysqli_close($connection);			
                        }
						mysqli_close($connection);
					}					
				    if  ($keyword=="2"){
					    if($connection){ 
	                        $sql = "SELECT Book_info.book_id,Books.book_name FROM Book_info,Books WHERE borrow_or_not=0 AND Books.book_id=Book_info.book_id";
	                        $book_list=mysqli_query($connection,$sql);
							$contentStr="当前还剩以下书籍可借~\n开头冒号前如“b001”的编号即为书籍编号~\n\n";
							while($row_booklist=mysqli_fetch_array($book_list,MYSQLI_NUM)){
							    $contentStr.="$row_booklist[0]:\n$row_booklist[1]\n";
							}
						    mysqli_free_result($book_list);
					    }
						mysqli_close($connection);
					}		
					//借书菜单
					if  ($keyword=="3"){
						if($connection){					
						    $sql="SELECT open_id,user_name FROM Users WHERE open_id = '$fromUsername' LIMIT 1";
							//$sql2="SELECT open_id FROM Users INNER JOIN Book_info WHERE open_id = '$fromUsername' AND Book_info.user_name=Users.user_name LIMIT 1";
						    $check_user=mysqli_query($connection,$sql);
							$row_check_user=mysqli_fetch_row($check_user);
						    if($row_check_user[0]!=$fromUsername){
						        $contentStr = "这是您第一次使用移维图书馆~\n请输入真实姓名,格式为关键字“姓名”加上您的名字，如：\n姓名吴君毅";
					        }else{
								//$sql2="SELECT open_id,FROM Users INNER JOIN Book_info WHERE open_id = '$fromUsername' AND Book_info.user_name=Users.user_name LIMIT 1";
								//$sql2="SELECT book_name FROM Books INNER JOIN Book_info INNER JOIN Users WHERE Users.open_id = '$fromUsername' AND Book_info.open_id=Users.open_id AND Book_info.book_id = Books.book_id LIMIT 1";
								$sql1="SELECT book_name FROM Books INNER JOIN Book_info WHERE open_id = '$fromUsername' AND Book_info.book_id = Books.book_id LIMIT 1";
								//$sql2="SELECT user_name FROM Users WHERE open_id = '$fromUsername'";
								$check_borrow=mysqli_query($connection,$sql1);
								$row_check_borrow=mysqli_fetch_row($check_borrow);
								if($row_check_borrow[0]==NULL){
							        $contentStr = "$row_check_user[1]您好~\n请输入要借的书籍，格式为关键字“借”加上书籍编号，如：\n借b011\n不知道书籍编号可以输入数字“1”查询哦~";
							    }else{
									$sql1="SELECT book_name,back_or_not,confirmed FROM Books,Order_info WHERE open_id = '$fromUsername' AND Books.book_id = Order_info.book_id ORDER BY order_id DESC LIMIT 1";
                                    $check_confirm=mysqli_query($connection,$sql1);
								    $row_check_confirm=mysqli_fetch_row($check_confirm);
								    if ($row_check_confirm[1]==1){
									    $contentStr = "您所还书籍“$row_check_confirm[0]”尚待确认~可联系管理员贠晓雪进行确认~";
								    }else{
								        $contentStr = "$row_check_user[1]您好~\n您有书籍”$row_check_borrow[0]”在借，请输入数字“4”归还后进行借书操作~";
								    }
								}
						    }
						}
						mysqli_close($connection);
					}
					//姓名确认
					if (mb_substr($keyword,0,2,'utf-8')=="姓名"){
						if($connection){
					        $yourname = mb_substr($keyword,2,4,'utf-8');
							$sql1="SELECT open_id,user_name FROM Users WHERE open_id = '$fromUsername' LIMIT 1";
							$check_user=mysqli_query($connection,$sql1);
							$row_check_user=mysqli_fetch_row($check_user);
							$sql2="REPLACE INTO Users(open_id,user_name) VALUES ('$fromUsername','$yourname')";
							$sql3="SELECT allusers FROM namelist WHERE allusers= '$yourname' LIMIT 1";
							$check_in=mysqli_query($connection,$sql3);
							$row_check_in=mysqli_fetch_row($check_in);
							if ($row_check_user[0]==NULL AND $row_check_in[0]!=NULL){
	                            mysqli_query($connection,$sql2);
						        $contentStr = "您的名字是：$yourname\n~请重新输入数字“3”进入借书功能~";
                            }
							if($row_check_user[0]==NULL AND $row_check_in[0]==NULL){
								$contentStr = "您的名字不在移动中心姓名库中，请重新输入关键字“姓名”加上您的名字";
							}
							if($row_check_user[0]!=NULL AND $row_check_user[1]==$yourname){
								$contentStr = "您的名字是：$yourname\n~请重新输入数字“3”进入借书功能~";
							}
							if($row_check_user[0]!=NULL AND $row_check_user[1]!=$yourname){
								$contentStr = "输入有误，您的名字为：$row_check_user[1]\n~请重新输入数字“3”进入借书功能~";
							}
                        }							
						mysqli_close($connection);
					}
					//借书操作
					if (mb_substr($keyword,0,1,'utf-8')=="借"){
						
						$borrow_book = mb_substr($keyword,1,4,'utf-8');
						if($connection){
							$sql="SELECT book_name FROM Books INNER JOIN Book_info INNER JOIN Users WHERE Users.open_id = '$fromUsername' AND Book_info.open_id=Users.open_id AND Book_info.book_id = Books.book_id LIMIT 1";
							$check_borrow=mysqli_query($connection,$sql);
							$row_check_borrow=mysqli_fetch_row($check_borrow);
							if ($row_check_borrow[0]!=NULL){
								$sql1="SELECT book_name,back_or_not,confirmed FROM Books,Order_info WHERE open_id = '$fromUsername' AND Books.book_id = Order_info.book_id ORDER BY order_id DESC LIMIT 1";
                                $check_confirm=mysqli_query($connection,$sql1);
								$row_check_confirm=mysqli_fetch_row($check_confirm);
								if ($row_check_confirm[1]==1){
									$contentStr = "您所还书籍“$row_check_confirm[0]”尚待确认~可联系管理员贠晓雪进行确认~";
								}else{
								    $contentStr = "对不起，您有书籍“$row_check_borrow[0]”在借，请输入数字“4”归还后进行借书操作~";
								}
							}else{
							    $sql1="SELECT book_id,borrow_or_not FROM Book_info WHERE book_id = '$borrow_book' LIMIT 1";
							    $check_book=mysqli_query($connection,$sql1);
							    $row_check_book=mysqli_fetch_row($check_book);
							    if ($row_check_book[0]!=null AND $row_check_book[1]==0){
								    $sql2="UPDATE Book_info INNER JOIN Users SET borrow_or_not=1,Book_info.open_id=Users.open_id WHERE book_id='$borrow_book' AND Users.open_id='$fromUsername'";
									$sql3="INSERT INTO Order_info(book_id,open_id,borrowdate,deadline) VALUES ('$borrow_book','$fromUsername',NOW(),DATE_ADD(NOW(),INTERVAL 2 MONTH))";
								    $sql4="SELECT book_name,date(deadline) FROM Books,Order_info WHERE open_id='$fromUsername' AND Books.book_id='$borrow_book' AND Order_info.book_id='$borrow_book' ORDER BY borrowdate DESC LIMIT 1";
								    mysqli_query($connection,$sql2);
								    mysqli_query($connection,$sql3);
								    $book_deadline=mysqli_query($connection,$sql4);
									
								    $row_book_deadline=mysqli_fetch_row($book_deadline);
								    $contentStr = "借书成功！您所借的书为：“$row_book_deadline[0]”\n请凭此消息至管理员贠晓雪处领取~领取后请在规定时间内进行还书并在该订阅号进行还书操作~\n截止日期为：\n$row_book_deadline[1]";
									//$contentStr1 = "您好~您所借书籍“$row_book_deadline[0]”将在5天内到期，截止日期为$row_book_deadline[1]~\n请尽快归还以免对下次借书造成影响！若已归还，请勿理会~";
									/* $timaback=strtotime("- 5 DAY 3 hour 20 minute",strtotime($row_book_deadline[1]));
									$msgType = "text";
                	                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $timeback, $msgType, $contentStr1);
                	                echo $resultStr; */
							    }elseif($row_check_book[0]!=null AND $row_check_book[1]==1){ 
									$sql5="SELECT date(deadline),user_name FROM Order_info ,Users WHERE Users.open_id = Order_info.open_id AND book_id='$borrow_book'  ORDER BY borrowdate DESC LIMIT 1";
								    $deadline_name=mysqli_query($connection,$sql5);
								    $row_deadline_name=mysqli_fetch_row($deadline_name);
								    $contentStr = "对不起！此书已借出，目前在$row_deadline_name[1]处，将于$row_deadline_name[0]归还~\n回复数字“2”查询剩余可借书籍~";
							    }else{
								    $contentStr = "对不起，该书不存在~请回复数字“1”查询所有书籍列表或回复数字“2”查询剩余可借~";
							    }
						    }
						}
						mysqli_close($connection);
					}
					//还书操作
					if  ($keyword=="4"){
						if ($connection){
							$sql1="SELECT book_name,borrow_or_not FROM Books,Book_info WHERE open_id = '$fromUsername' AND Books.book_id = Book_info.book_id";
							$check_user_borrow=mysqli_query($connection,$sql1);
							$row_check_user_borrow=mysqli_fetch_row($check_user_borrow);
							if ($row_check_user_borrow[1]!=1){
								$contentStr = "您暂无书籍在借，无需还书~";
							}else{
								//$sql2="UPDATE Book_info SET borrow_or_not=0,open_id=NULL WHERE open_id='$fromUsername'";
								//mysqli_query($connection,$sql2);
								$sql2="SELECT book_name,back_or_not,confirmed FROM Books,Order_info WHERE open_id = '$fromUsername' AND Books.book_id = Order_info.book_id ORDER BY order_id DESC LIMIT 1";
								$check_confirm=mysqli_query($connection,$sql2);
								$row_check_confirm=mysqli_fetch_row($check_confirm);
								if ($row_check_confirm[1]==1){
									$contentStr = "您所还书籍“$row_check_user_borrow[0]”尚待确认~可联系管理员贠晓雪进行确认~";
								}else{
								    $sql3="UPDATE Order_info SET backdate=NOW(),back_or_not=1 WHERE open_id='$fromUsername' ORDER BY order_id DESC LIMIT 1";
								    mysqli_query($connection,$sql3);
								    $contentStr = "您所借书籍“$row_check_user_borrow[0]”已还~待管理员确认后可以输入数字“3”重新借书啦~";
								}
							}
						}
						mysqli_close($connection);
					}
					//查看书籍详细信息
					if (mb_substr($keyword,0,1,'utf-8')=="b"){
					    if ($connection){
						    $sql1="SELECT Books.book_id,book_name,douban_url,jd_url,borrow_or_not FROM Books,Book_info WHERE Books.book_id='$keyword' AND Books.book_id=Book_info.book_id";
						    $check_bookinfo=mysqli_query($connection,$sql1);
						    $row_check_bookinfo=mysqli_fetch_row($check_bookinfo);
						    if($row_check_bookinfo[1]==NULL){
							    $contentStr="对不起，输入的书籍编号不正确，可输入数字“1”查询书籍编号";
						    }else{
								if ($row_check_bookinfo[4]==0){
									$contentStr="书籍编号：$row_check_bookinfo[0]\n书名：<a href='$row_check_bookinfo[2]'>$row_check_bookinfo[1]</a>\n本书还躺在图书馆里，速速去借吧~\n点击书名可进入本书豆瓣读书链接查看详情及评论~\n点击<a href='$row_check_bookinfo[3]'>这里</a>直接上京东购买哦~";
								}else{
									$sql2="SELECT date(deadline),user_name FROM Order_info ,Users WHERE Users.open_id = Order_info.open_id AND book_id='$keyword' ORDER BY borrowdate DESC LIMIT 1";
								    $check_book_now=mysqli_query($connection,$sql2);
						            $row_check_book_now=mysqli_fetch_row($check_book_now);
									$contentStr="书籍编号：$row_check_bookinfo[0]\n书名：<a href='$row_check_bookinfo[2]'>$row_check_bookinfo[1]</a>\n书已经在$row_check_book_now[1]手中咯~不过他最晚$row_check_book_now[0]就会还啦~\n点击书名可进入本书豆瓣读书链接查看详情及评论~\n点击<a href='$row_check_bookinfo[3]'>这里</a>直接上京东购买哦~";
								}
								
						    }
					    }
					    mysqli_close($connection);
					}
					//管理员专用：查看5日内归还及逾期
					if ($keyword=="8" AND ($fromUsername=="oMwWKjlU2bW9zFprLa7fJ-2MAmsQ" OR $fromUsername=="oo8emwIceaGfzsa_MryogrA2EBm4")){
						if ($connection){
						    $sql1="SELECT Order_info.book_id,deadline,book_name,user_name,TO_DAYS(date(deadline))-TO_DAYS(NOW()) FROM Order_info,Books,Users WHERE back_or_not=0 AND Books.book_id = Order_info.book_id AND Users.open_id = Order_info.open_id AND (TO_DAYS(date(deadline))-TO_DAYS(NOW())>=0) AND (TO_DAYS(date(deadline))-TO_DAYS(NOW())<=5)";
						    $day_5_back=mysqli_query($connection,$sql1);
						    $contentStr1="1. 需要5日内还书的人为：\n\n";
						        while($row_day_5_back=mysqli_fetch_array($day_5_back,MYSQLI_NUM)){
							        $contentStr1.="$row_day_5_back[3]: 需在$row_day_5_back[4]日内归还“$row_day_5_back[2]”\n";
						        }
						    $sql2="SELECT Order_info.book_id,deadline,book_name,user_name,TO_DAYS(NOW())-TO_DAYS(date(deadline)) FROM Order_info,Books,Users WHERE back_or_not=0 AND Books.book_id = Order_info.book_id AND Users.open_id = Order_info.open_id AND (TO_DAYS(date(deadline))-TO_DAYS(NOW())<0)";
						    $late=mysqli_query($connection,$sql2);
						    $contentStr2="\n2. 已逾期的人为：\n\n";
						        while ($row_late=mysqli_fetch_array($late,MYSQLI_NUM)){
							        $contentStr2.="$row_late[3]: 需归还“$row_late[2]”，已逾期$row_late[4]天";
						        }
							if ($contentStr1=="1. 需要5日内还书的人为：\n\n" ){
							    $contentStr="1. 需要5日内还书的人为：\n\n暂无\n".$contentStr2;
							}
							if ($contentStr2=="\n2. 已逾期的人为：\n\n"){
								$contentStr=$contentStr1."\n2. 已逾期的人为：\n\n暂无";
							}
							if ($contentStr1!="1. 需要5日内还书的人为：\n\n" AND $contentStr2!="\n2. 已逾期的人为：\n\n"){
								$contentStr=$contentStr1.$contentStr2;
							}
						    mysqli_free_result($day_5_back);
							mysqli_free_result($late);
						}	
					    mysqli_close($connection);
					}
                    					
					//管理员专用：确认还书	
					if ($keyword=="9" AND ($fromUsername=="oMwWKjlU2bW9zFprLa7fJ-2MAmsQ" OR $fromUsername=="oo8emwIceaGfzsa_MryogrA2EBm4")){
                        if($connection){
							$sql="SELECT book_name,user_name,date(backdate),order_id FROM Books,Users,Order_info WHERE back_or_not=1 AND confirmed=0 AND Books.book_id = Order_info.book_id AND Users.open_id=Order_info.open_id ORDER BY borrowdate DESC";
							$check_confirmed=mysqli_query($connection,$sql);
							//$contentStr1="以下还书待确认：\n\n";
							$contentStr1="";
							while($row_check_confirmed=mysqli_fetch_array($check_confirmed,MYSQLI_NUM)){
								$contentStr1.="订单$row_check_confirmed[3]：$row_check_confirmed[1]还书“$row_check_confirmed[0]”于$row_check_confirmed[2]\n";
							}
							if ($contentStr1==NULL){
								$contentStr="暂无待确认的还书操作~";
							}else{
								$contentStr="以下还书待确认，请输入关键字“确认”加上订单编号，如“确认33”进行确认操作~以下各行最开始的编号即为订单编号哦~\n确认所有订单请输入“确认全部”~\n\n".$contentStr1;
							}
						}
                        mysqli_close($connection);
					}		 
 					if (mb_substr($keyword,0,2,'utf-8')=="确认" AND ($fromUsername=="oMwWKjlU2bW9zFprLa7fJ-2MAmsQ" OR $fromUsername=="oo8emwIceaGfzsa_MryogrA2EBm4")){
					    $get_order_id=mb_substr($keyword,2,4,'utf-8');
						if ($connection){
							if ($get_order_id=="全部"){
								$sqlall="SELECT book_name,user_name,date(backdate),order_id,Order_info.book_id,Order_info.open_id FROM Books,Users,Order_info WHERE back_or_not=1 AND confirmed=0 AND Books.book_id = Order_info.book_id AND Users.open_id=Order_info.open_id ORDER BY borrowdate DESC";
								$check_confirmed=mysqli_query($connection,$sqlall);
								while($row_check_confirmed=mysqli_fetch_array($check_confirmed,MYSQLI_NUM)){
									$sql_confirm="UPDATE Order_info SET confirmed=1 WHERE order_id='$row_check_confirmed[3]'";
									mysqli_query($connection,$sql_confirm);
			                        $sql_borrow="UPDATE Book_info SET borrow_or_not=0,open_id=NULL WHERE book_id='$row_check_confirmed[4]' AND open_id='$row_check_confirmed[5]'";
									mysqli_query($connection,$sql_borrow);									
								}
								$contentStr="当前待确认还书已全部确认";	
							}else{
								$sql1="SELECT order_id,Order_info.book_id,Order_info.open_id,date(backdate),back_or_not,confirmed,book_name,user_name FROM Order_info,Books,Users WHERE order_id='$get_order_id' AND Books.book_id = Order_info.book_id AND Users.open_id=Order_info.open_id";
								$check_order=mysqli_query($connection,$sql1);
								$row_check_order=mysqli_fetch_row($check_order);
								if($row_check_order[4]==1 AND $row_check_order[5]==0){
									$sql2="UPDATE Order_info SET confirmed =1 WHERE order_id='$get_order_id'";
									mysqli_query($connection,$sql2);
									//$sql3="SELECT order_id,date(backdate),Order_info.book_id,Order_info.open_id,book_name,user_name FROM Order_info,Books,Users WHERE order_id='$get_order_id' AND Books.book_id = Order_info.book_id AND Users.open_id=Order_info.open_id LIMIT 1";
									//$check3=mysqli_query($connection,$sql3);
									//$row_check3=$mysqli_fetch_row($check3);
									$sql3="UPDATE Book_info SET borrow_or_not=0,open_id=NULL WHERE book_id='$row_check_order[1]' AND open_id='$row_check_order[2]'";
								    mysqli_query($connection,$sql3);
									$contentStr="订单$row_check_order[0]：$row_check_order[7]于\n$row_check_order[3]还书$row_check_order[6]已确认~\n重新输入数字“9”处理其余待确认还书~";
								}else{
									$contentStr="订单编号输入错误，请重新输入~";	
								}	
							}
                        }
                        mysqli_close($connection);
                    }						
					
					$msgType = "text";
                	//$contentStr = "Welcome to wechat world!";       //在此处定义自动回复内容
                	$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                	echo $resultStr;
					//mysqli_close($connection);
				    //}  
                }elseif($keyword=="0"){
					if ($fromUsername=="oMwWKjlU2bW9zFprLa7fJ-2MAmsQ" OR $fromUsername=="oo8emwIceaGfzsa_MryogrA2EBm4"){
						$contentStr = "大家好，欢迎来到移动维护中心维图书馆~请输入以下数字进行相应操作:\n1：查询所有书籍编号列表\n2：查询剩余可借\n3：我要借书\n4：我要还书\n8：查看需在5日内归还的用户及已逾期的用户\n9；对当前待确认的归还行为进行确认操作\n0：回主菜单\n直接输入书籍编号可查看本书豆瓣图书链接、当前状态及在哪位亲的手中哦~";
					}else{
					    $contentStr = "大家好，欢迎来到移动维护中心维图书馆~请输入以下数字进行相应操作:\n1：查询所有书籍编号列表\n2：查询剩余可借\n3：我要借书\n4：我要还书\n0：回主菜单\n直接输入书籍编号可查看本书豆瓣图书链接、当前状态及在哪位亲的手中哦~";
					}
				    $msgType = "text";
                	$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                	echo $resultStr;
				}else{
                	echo "Input something...";
                }
        }else {
        	echo "";
        	exit;
        }
    }
		
	private function checkSignature()
	{
        // you must define TOKEN by yourself
        if (!defined("TOKEN")) {
            throw new Exception('TOKEN is not defined!');
        }
        
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        		
		$token = TOKEN;
		$tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
		sort($tmpArr, SORT_STRING);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		
		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}
}

?>