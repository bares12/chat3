<?php

/*
  copyright @ medantechno.com
  2017

 */

require_once('./line_class.php');
require_once('./unirest-php-master/src/Unirest.php');

$channelSecret = '941b173d5a8b59b29b2bc1d00657f826'; //sesuaikan
$channelAccessToken = 'W87tpLbjGorG1Oinv3DWM8XdNriJ2NsCmnos6VaI6D5obHTIM6NkC/UUMN24XdpAduwc5YDuFV45gQqRxVt3Ibu1O4CgRbCNJU+lru5RumhP0vYeFMgtycbiNOz3gQGwsNgGjXloAaqV1rj5S4ma0QdB04t89/1O/w1cDnyilFU='; //sesuaikan

$client = new LINEBotTiny($channelAccessToken, $channelSecret);

//var_dump($client->parseEvents());
//$_SESSION['userId']=$client->parseEvents()[0]['source']['userId'];

/*
  {
  "replyToken": "nHuyWiB7yP5Zw52FIkcQobQuGDXCTA",
  "type": "message",
  "timestamp": 1462629479859,
  "source": {
  "type": "user",
  "userId": "U206d25c2ea6bd87c17655609a1c37cb8"
  },
  "message": {
  "id": "325708",
  "type": "text",
  "text": "Hello, world"
  }
  }
 */


$userId = $client->parseEvents()[0]['source']['userId'];
$groupId = $client->parseEvents()[0]['source']['groupId'];
$replyToken = $client->parseEvents()[0]['replyToken'];
$timestamp = $client->parseEvents()[0]['timestamp'];
$type = $client->parseEvents()[0]['type'];

$message = $client->parseEvents()[0]['message'];
$messageid = $client->parseEvents()[0]['message']['id'];

$profil = $client->profil($userId);

$pesan_datang = explode(" ", $message['text']);

$command = $pesan_datang[0];
$options = $pesan_datang[1];
if (count($pesan_datang) > 2) {
    for ($i = 2; $i < count($pesan_datang); $i++) {
        $options .= '+';
        $options .= $pesan_datang[$i];
    }
}

function img_search($keyword) {
    $uri = 'https://www.google.co.id/search?q=' . $keyword . '&safe=off&source=lnms&tbm=isch';

    $response = Unirest\Request::get("$uri");

    $hasil = str_replace(">", "&gt;", $response->raw_body);
    $arrays = explode("<", $hasil);
    return explode('"', $arrays[291])[3];
}

function anime($keyword) {

    $fullurl = 'https://myanimelist.net/api/anime/search.xml?q=' . $keyword;
    $username = 'jamal3213';
    $password = 'FZQYeZ6CE9is';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_VERBOSE, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_URL, $fullurl);

    $returned = curl_exec($ch);
    $xml = new SimpleXMLElement($returned);
    $parsed = array();

    $parsed['id'] = (string) $xml->entry[0]->id;
    $parsed['image'] = (string) $xml->entry[0]->image;
    $parsed['title'] = (string) $xml->entry[0]->title;
    $parsed['desc'] = "Episode : ";
    $parsed['desc'] .= $xml->entry[0]->episodes;
    $parsed['desc'] .= "\nNilai : ";
    $parsed['desc'] .= $xml->entry[0]->score;
    $parsed['desc'] .= "\nTipe : ";
    $parsed['desc'] .= $xml->entry[0]->type;
    $parsed['synopsis'] = str_replace("<br />", "\n", html_entity_decode((string) $xml->entry[0]->synopsis, ENT_QUOTES | ENT_XHTML, 'UTF-8'));
    return $parsed;
}

function anime_syn($title) {
    $parsed = anime($title);
    $result = "Judul : " . $parsed['title'];
    $result .= "\n\nSynopsis :\n" . $parsed['synopsis'];
    return $result;
}

function urb_dict($keyword) {
    $uri = "http://api.urbandictionary.com/v0/define?term=" . $keyword;

    $response = Unirest\Request::get("$uri");


    $json = json_decode($response->raw_body, true);
    $result = $json['list'][0]['definition'];
    $result .= "\n\nExamples : \n";
    $result .= $json['list'][0]['example'];
    return $result;
}

#---------------------[IMDB Scraper]---------------------#
function imdb_scraper($keyword) {
    $uri = "http://www.omdbapi.com/?t=" . $keyword . '&plot=full&apikey=d5010ffe';

    $response = Unirest\Request::get("$uri");


    $json = json_decode($response->raw_body, true);
    $title = $json['Title'];
	$year = $json['Year'];
	$genre = $json['Genre'];
	$plot = $json['Plot'];
	$poster = $json['Poster'];
    return $result;
}

function film_syn($keyword) {
    $uri = "http://www.omdbapi.com/?t=" . $keyword . '&plot=full&apikey=d5010ffe';

    $response = Unirest\Request::get("$uri");

    $json = json_decode($response->raw_body, true);
    $result = "Judul : \n";
	$result .= $json['Title'];
	$result .= "\n\nSinopsis : \n";
	$result .= $json['Plot'];
    return $result;
}
#---------------------[IMDB Scraper]---------------------#

#---------------------[Shalat Scraper]---------------------#
function shalat($keyword) {
    $uri = "https://time.siswadi.com/pray/" . $keyword;

    $response = Unirest\Request::get("$uri");

    $json = json_decode($response->raw_body, true);
    $result = "Jadwal Shalat Sekitar ";
	$result .= $json['location']['address'];
	$result .= "\nTanggal : ";
	$result .= $json['time']['date'];
	$result .= "\n\nShubuh : ";
	$result .= $json['data']['Fajr'];
	$result .= "\nDzuhur : ";
	$result .= $json['data']['Dhuhr'];
	$result .= "\nAshar : ";
	$result .= $json['data']['Asr'];
	$result .= "\nMaghrib : ";
	$result .= $json['data']['Maghrib'];
	$result .= "\nIsya : ";
	$result .= $json['data']['Isha'];
    return $result;
}	
#---------------------[Shalat Scraper]---------------------#

#---------------------[Cuaca]---------------------#
function cuaca($keyword) {
    $uri = "http://api.openweathermap.org/data/2.5/weather?q=" . $keyword . ",ID&units=metric&appid=e172c2f3a3c620591582ab5242e0e6c4";

    $response = Unirest\Request::get("$uri");

    $json = json_decode($response->raw_body, true);
    $result = "Ramalan Cuaca Di ";
	$result .= $json['name'];
	$result .= "\n\nCuaca : ";
	$result .= $json['weather']['0']['main'];
	$result .= "\nDeskripsi : ";
	$result .= $json['weather']['0']['description'];
    return $result;
}	
#---------------------[Cuaca]---------------------#

#---------------------[SAVEITOFFLINE - YT]---------------------#
function saveitoffline($keyword) {
    $uri = "https://www.saveitoffline.com/process/?url=" . $keyword . '&type=json';

    $response = Unirest\Request::get("$uri");


    $json = json_decode($response->raw_body, true);
	$result = "Judul : \n";
	$result .= $json['title'];
	$result .= "\n\nUkuran : \n";
	$result .= $json['urls'][0]['label'];
	$result .= "\n\nURL Download : \n";
	$result .= $json['urls'][0]['id'];
	$result .= "\n\nUkuran : \n";
	$result .= $json['urls'][1]['label'];
	$result .= "\n\nURL Download : \n";
	$result .= $json['urls'][1]['id'];
	$result .= "\n\nUkuran : \n";
	$result .= $json['urls'][2]['label'];	
	$result .= "\n\nURL Download : \n";
	$result .= $json['urls'][2]['id'];
	$result .= "\n\nUkuran : \n";
	$result .= $json['urls'][3]['label'];	
	$result .= "\n\nURL Download : \n";
	$result .= $json['urls'][3]['id'];	
    return $result;
}
#---------------------[SAVEITOFFLINE - YT]---------------------#

//show menu, saat join dan command /menu
if ($type == 'join' || $command == 'help') {
    $text = "Mau Lihat Command? Ketik /keyword";
    $balas = array(
        'replyToken' => $replyToken,
        'messages' => array(
            array(
                'type' => 'text',
                'text' => $text
            )
        )
    );
}
//pesan bergambar
if ($message['type'] == 'text') {
    if ($command == 'def') {


        $balas = array(
            'replyToken' => $replyToken,
            'messages' => array(
                array(
                    'type' => 'text',
                    'text' => 'Definition : ' . urb_dict($options)
                )
            )
        );
    } else if ($command == '/keluar') {

$push = array(
							'to' => $groupId,									
							'messages' => array(
								array(
										'type' => 'text',					
										'text' => 'here is my poo...'
									)
							)
						);
						
		
		$client->pushMessage($push);

        $psn = $client->leaveGroup($groupId);
    } else if ($command == '/img') {
        $hasil = img_search($options);
        $balas = array(
            'replyToken' => $replyToken,
            'messages' => array(
                array(
                    'type' => 'image',
                    'originalContentUrl' => $hasil,
                    'previewImageUrl' => $hasil
                )
            )
        );
    } else if ($command == '/pika') {
        $keyword = 'Zl_ZeIMHWjc';
        $image = 'https://img.youtube.com/vi/' . $keyword . '/2.jpg';
        $balas = array(
            'replyToken' => $replyToken,
            'messages' => array(
                array(
                    'type' => 'image',
                    'originalContentUrl' => $image,
                    'previewImageUrl' => $image
                ), array(
                    'type' => 'video',
                    'originalContentUrl' => vid_search($keyword),
                    'previewImageUrl' => $image
                )
            )
        );
    } else if ($command == '/anime') {
        $result = anime($options);
        $altText = "Title : " . $result['title'];
        $altText .= "\n\n" . $result['desc'];
        $altText .= "\nMAL Page : https://myanimelist.net/anime/" . $result['id'];
        $balas = array(
            'replyToken' => $replyToken,
            'messages' => array(
                array(
                    'type' => 'template',
                    'altText' => $altText,
                    'template' => array(
                        'type' => 'buttons',
                        'title' => $result['title'],
                        'thumbnailImageUrl' => $result['image'],
                        'text' => $result['desc'],
                        'actions' => array(
                            array(
                                'type' => 'postback',
                                'label' => 'Baca Sinopsis-nya',
                                'data' => 'action=add&itemid=123',
                                'text' => '/anime-syn ' . $options
                            ),
                            array(
                                'type' => 'uri',
                                'label' => 'Website MAL',
                                'uri' => 'https://myanimelist.net/anime/' . $result['id']
                            )
                        )
                    )
                )
            )
        );
    } else if ($command == '/anime-syn') {

        $result = anime_syn($options);
        $balas = array(
            'replyToken' => $replyToken,
            'messages' => array(
                array(
                    'type' => 'text',
                    'text' => $result
                )
            )
        );
    } else if ($command == '/shalat') {

        $result = shalat($options);
        $balas = array(
            'replyToken' => $replyToken,
            'messages' => array(
                array(
                    'type' => 'text',
                    'text' => $result
                )
            )
        );
    } else if ($command == '/cuaca') {

        $result = cuaca($options);
        $balas = array(
            'replyToken' => $replyToken,
            'messages' => array(
                array(
                    'type' => 'text',
                    'text' => $result
                )
            )
        );
    } else if ($command == '/film-syn') {				#---------------------[TAMBAHAN FARZAIN]---------------------#

        $result = film_syn($options);
        $balas = array(
            'replyToken' => $replyToken,
            'messages' => array(
                array( 
                    'type' => 'text',
                    'text' => film_syn($options)
                )
            )
        );												#---------------------[TAMBAHAN FARZAIN]---------------------#
    } else if ($command == '/yt') { 					#---------------------[TAMBAHAN FARZAIN]---------------------#

        $result = saveitoffline($options);
        $balas = array(
            'replyToken' => $replyToken,
            'messages' => array(
                array(
                    'type' => 'text',
                    'text' => saveitoffline($options)
                ),
				array(
				    'type' => 'text',
				    'text' => 'Silahkan Kalian Copy URL Download Yang Tersedia Diatas Sesuai Dengan Ukuran Yang Anda Inginkan, Dan Paste Di Browser HP Kalian'
				)
            )
        );												#---------------------[TAMBAHAN FARZAIN]---------------------#
    } else if ($command == '/keyword') {
	
	        $balas = array(
							'replyToken' => $replyToken,
							'messages' => array(
								array (
										  'type' => 'template',
										  'altText' => 'Menu',
										  'template' => 
										  array (
										    'type' => 'carousel',
										    'columns' => 
										    array (
										      0 => 
										      array (
										        'thumbnailImageUrl' => 'https://img.monocle.com/radio/shows/the-menu-final-5718a9900140e.jpg?w=440&h=440',
										        'title' => '[DK] BOT',
										        'text' => 'Developer Support',
										        'actions' => 
										        array (
										          0 => 
										          array (
										            'type' => 'postback',
										            'label' => 'Welcome',
										            'data' => 'action=add&itemid=111',
													'text' => 'Welcome'
										          ),
										          1 => 
										          array (
										            'type' => 'postback',
										            'label' => 'Admin',
										            'data' => 'action=add&itemid=111',
													'text' => 'Admin'
												  ),
										          2 => 
										          array (
										            'type' => 'postback',
										            'label' => 'About',
										            'data' => 'action=add&itemid=111',
													'text' => 'About'
										          ),
										        ),
										      ),
										      1 => 
										      array (
										        'thumbnailImageUrl' => 'https://img.monocle.com/radio/shows/the-menu-final-5718a9900140e.jpg?w=440&h=440',
										        'title' => '[DK] BOT',
										        'text' => 'Silahkan Di Klik Saja!',
										        'actions' => 
										        array (
										          0 => 
										          array (
										            'type' => 'postback',
										            'label' => 'Spam',
										            'data' => 'action=add&itemid=111',
													'text' => 'Spam'
										          ),
										          1 => 
										          array (
													'type' => 'postback',
													'label' => 'Open Pagi',
													'data' => 'action=add&itemid=111',
													'text' => 'Open Pagi'
										          ),
										          2 => 
										          array (
													'type' => 'postback',
													'label' => 'Open Malam',
													'data' => 'action=add&itemid=111',
													'text' => 'Open Malam'
										          ),
										        ),
										      ),
										      2 => 
										      array (
										        'thumbnailImageUrl' => 'https://img.monocle.com/radio/shows/the-menu-final-5718a9900140e.jpg?w=440&h=440',
										        'title' => '[DK] BOT',
										        'text' => 'Info Lainnya Silakan Klik Lebih Lanjut..',
										        'actions' => 
										        array (
										          0 => 
										          array (
										            'type' => 'postback',
										            'label' => 'Menu 1',
										            'data' => 'action=add&itemid=111',
													'text' => 'menu1'
										          ),
										          1 => 
										          array (
													'type' => 'postback',
													'label' => 'Menu 2',
													'data' => 'action=add&itemid=111',
													'text' => 'menu2'
										          ),
										          2 => 
										          array (
													'type' => 'postback',
													'label' => 'Menu 3',
													'data' => 'action=add&itemid=111',
													'text' => 'menu3'
										          ),
										        ),
										      ),											  
										    ),
										  ),
										)					
			 
        )
        );												#---------------------[TAMBAHAN FARZAIN]---------------------#
    } else if ($command == 'Admin') {
	
	        $balas = array(
							'replyToken' => $replyToken,
							'messages' => array(
								array (
										  'type' => 'template',
										  'altText' => 'Ini Admin',
										  'template' => 
										  array (
										    'type' => 'carousel',
										    'columns' => 
										    array (
										      0 => 
										      array (
										        'thumbnailImageUrl' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQZjqnLG-Q6Qp7M1itw9GXL1hlLD18gnlD4b0beeokPDEX5TXjx',
										        'title' => 'Deka Prabowo',
										        'text' => 'Kalo Butuh Bantuan Silakan Chat ^_^',
										        'actions' => 
										        array (
										          0 => 
										          array (
													'type' => 'uri',
													'label' => 'Chat Line',
													'data' => 'action=add&itemid=111',
													'uri' => 'https://bit.ly/2J3ywc3'
										          ),
										          1 => 
										          array (
													'type' => 'uri',
													'label' => 'Instagram',
													'data' => 'action=add&itemid=111',
													'uri' => 'https://instagram.com/akedakad'
												  ),
										          2 => 
										          array (
										            'type' => 'postback',
										            'label' => 'Payment',
										            'data' => 'action=add&itemid=111',
													'text' => 'Payment'
										          ),
										        ),
										      ),											  
										    ),
										  ),
										)					
			 
        )
        );												#---------------------[TAMBAHAN FARZAIN]---------------------#
    } else if ($command == '/keyword') {
	
	        $balas = array(
							'replyToken' => $replyToken,
							'messages' => array(
								array (
										  'type' => 'template',
										  'altText' => 'Menu',
										  'template' => 
										  array (
										    'type' => 'carousel',
										    'columns' => 
										    array (
										      0 => 
										      array (
										        'thumbnailImageUrl' => 'https://img.monocle.com/radio/shows/the-menu-final-5718a9900140e.jpg?w=440&h=440',
										        'title' => '[DK] BOT',
										        'text' => 'Pemahaman Software',
										        'actions' => 
										        array (
										          0 => 
										          array (
										            'type' => 'postback',
										            'label' => 'Cara Menggunakan',
										            'data' => 'action=add&itemid=111',
													'text' => 'Cara Menggunakan'
										          ),
										          1 => 
										          array (
										            'type' => 'postback',
										            'label' => 'Arti Akun Limit',
										            'data' => 'action=add&itemid=111',
													'text' => 'Arti Akun Limit'
												  ),
										          2 => 
										          array (
										            'type' => 'postback',
										            'label' => 'Arti Akun Tumbal',
										            'data' => 'action=add&itemid=111',
													'text' => 'Arti Akun Tumbal'
										          ),
										        ),
										      ),
										      1 => 
										      array (
										        'thumbnailImageUrl' => 'https://img.monocle.com/radio/shows/the-menu-final-5718a9900140e.jpg?w=440&h=440',
										        'title' => '[DK] BOT',
										        'text' => 'Jenis Jenis Follower',
										        'actions' => 
										        array (
										          0 => 
										          array (
										            'type' => 'postback',
										            'label' => 'Follower Aktif',
										            'data' => 'action=add&itemid=111',
													'text' => 'Follower Aktif'
										          ),
										          1 => 
										          array (
													'type' => 'postback',
													'label' => 'Follower Pasif',
													'data' => 'action=add&itemid=111',
													'text' => 'Follower Pasif'
										          ),
										          2 => 
										          array (
													'type' => 'postback',
													'label' => 'Indo Or Worldwide',
													'data' => 'action=add&itemid=111',
													'text' => 'Indo Or Worldwide'
										          ),
										        ),
										      ),
										      2 => 
										      array (
										        'thumbnailImageUrl' => 'https://img.monocle.com/radio/shows/the-menu-final-5718a9900140e.jpg?w=440&h=440',
										        'title' => '[DK] BOT',
										        'text' => 'Event For You',
										        'actions' => 
										        array (
										          0 => 
										          array (
										            'type' => 'postback',
										            'label' => 'Peraturan Event',
										            'data' => 'action=add&itemid=111',
													'text' => 'Peraturan Event'
										          ),
										          1 => 
										          array (
													'type' => 'postback',
													'label' => 'Cara Mengikutin Event',
													'data' => 'action=add&itemid=111',
													'text' => 'Cara Mengikutin Event'
										          ),
										          2 => 
										          array (
													'type' => 'postback',
													'label' => 'Contact Admin',
													'data' => 'action=add&itemid=111',
													'text' => 'Admin'
										          ),
										        ),
										      ),											  
										    ),
										  ),
										)					
			 
        )
        );												#---------------------[TAMBAHAN FARZAIN]---------------------#
    } else if ($command == 'menu2') {
	
	        $balas = array(
							'replyToken' => $replyToken,
							'messages' => array(
								array (
										  'type' => 'template',
										  'altText' => 'Daftar Menu',
										  'template' => 
										  array (
										    'type' => 'carousel',
										    'columns' => 
										    array (
										      0 => 
										      array (
										        'thumbnailImageUrl' => 'https://img.monocle.com/radio/shows/the-menu-final-5718a9900140e.jpg?w=440&h=440',
										        'title' => 'Keyword 1',
										        'text' => 'Silahkan Dipilih',
										        'actions' => 
										        array (
										          0 => 
										          array (
										            'type' => 'postback',
										            'label' => 'Cari Anime',
										            'data' => 'action=add&itemid=111',
													'text' => 'Ketik /anime [Judul Anime]'
										          ),
										          1 => 
										          array (
										            'type' => 'postback',
										            'label' => 'Cari Sinopsis Anime',
										            'data' => 'action=add&itemid=111',
													'text' => 'Ketik /anime-syn [Judul Anime]'
												  ),
										          2 => 
										          array (
										            'type' => 'postback',
										            'label' => 'Cari Youtube',
										            'data' => 'action=add&itemid=111',
													'text' => 'Ketik /yt [URL Video Youtube]'
										          ),
										        ),
										      ),
										      1 => 
										      array (
										        'thumbnailImageUrl' => 'https://img.monocle.com/radio/shows/the-menu-final-5718a9900140e.jpg?w=440&h=440',
										        'title' => 'Keyword 2',
										        'text' => 'Silahkan Dipilih',
										        'actions' => 
										        array (
										          0 => 
										          array (
										            'type' => 'postback',
										            'label' => 'Cari Film',
										            'data' => 'action=add&itemid=111',
													'text' => 'Ketik /film [Judul Film]'
										          ),
										          1 => 
										          array (
													'type' => 'postback',
													'label' => 'Cari Sinopsis Film',
													'data' => 'action=add&itemid=111',
													'text' => 'Ketik /film-syn [Judul Film]'
										          ),
										          2 => 
										          array (
													'type' => 'postback',
													'label' => 'Cari Gambar',
													'data' => 'action=add&itemid=111',
													'text' => 'Ketik /gambar [Kata Kunci]'
										          ),
										        ),
										      ),
										      2 => 
										      array (
										        'thumbnailImageUrl' => 'https://img.monocle.com/radio/shows/the-menu-final-5718a9900140e.jpg?w=440&h=440',
										        'title' => 'Keyword 3',
										        'text' => 'Silahkan Dipilih',
										        'actions' => 
										        array (
										          0 => 
										          array (
										            'type' => 'postback',
										            'label' => 'Jadwal Shalat',
										            'data' => 'action=add&itemid=111',
													'text' => 'Ketik /shalat [Lokasi]'
										          ),
										          1 => 
										          array (
													'type' => 'postback',
													'label' => 'Cari Sinopsis Film',
													'data' => 'action=add&itemid=111',
													'text' => 'Ketik /cuaca [Lokasi]'
										          ),
										          2 => 
										          array (
													'type' => 'uri',
													'label' => 'Admin',
													'data' => 'action=add&itemid=111',
													'uri' => 'https://bit.ly/2J3ywc3'
										          ),
										        ),
										      ),											  
										    ),
										  ),
										)					
			 
        )
        );												#---------------------[TAMBAHAN FARZAIN]---------------------#
    } else if ($command == 'menu3') {
	
	        $balas = array(
							'replyToken' => $replyToken,
							'messages' => array(
								array (
										  'type' => 'template',
										  'altText' => 'Daftar Menu',
										  'template' => 
										  array (
										    'type' => 'carousel',
										    'columns' => 
										    array (
										      0 => 
										      array (
										        'thumbnailImageUrl' => 'https://img.monocle.com/radio/shows/the-menu-final-5718a9900140e.jpg?w=440&h=440',
										        'title' => 'Keyword 1',
										        'text' => 'Silahkan Dipilih',
										        'actions' => 
										        array (
										          0 => 
										          array (
										            'type' => 'postback',
										            'label' => 'Cari Anime',
										            'data' => 'action=add&itemid=111',
													'text' => 'Ketik /anime [Judul Anime]'
										          ),
										          1 => 
										          array (
										            'type' => 'postback',
										            'label' => 'Cari Sinopsis Anime',
										            'data' => 'action=add&itemid=111',
													'text' => 'Ketik /anime-syn [Judul Anime]'
												  ),
										          2 => 
										          array (
										            'type' => 'postback',
										            'label' => 'Cari Youtube',
										            'data' => 'action=add&itemid=111',
													'text' => 'Ketik /yt [URL Video Youtube]'
										          ),
										        ),
										      ),
										      1 => 
										      array (
										        'thumbnailImageUrl' => 'https://img.monocle.com/radio/shows/the-menu-final-5718a9900140e.jpg?w=440&h=440',
										        'title' => 'Keyword 2',
										        'text' => 'Silahkan Dipilih',
										        'actions' => 
										        array (
										          0 => 
										          array (
										            'type' => 'postback',
										            'label' => 'Cari Film',
										            'data' => 'action=add&itemid=111',
													'text' => 'Ketik /film [Judul Film]'
										          ),
										          1 => 
										          array (
													'type' => 'postback',
													'label' => 'Cari Sinopsis Film',
													'data' => 'action=add&itemid=111',
													'text' => 'Ketik /film-syn [Judul Film]'
										          ),
										          2 => 
										          array (
													'type' => 'postback',
													'label' => 'Cari Gambar',
													'data' => 'action=add&itemid=111',
													'text' => 'Ketik /gambar [Kata Kunci]'
										          ),
										        ),
										      ),
										      2 => 
										      array (
										        'thumbnailImageUrl' => 'https://img.monocle.com/radio/shows/the-menu-final-5718a9900140e.jpg?w=440&h=440',
										        'title' => 'Keyword 3',
										        'text' => 'Silahkan Dipilih',
										        'actions' => 
										        array (
										          0 => 
										          array (
										            'type' => 'postback',
										            'label' => 'Jadwal Shalat',
										            'data' => 'action=add&itemid=111',
													'text' => 'Ketik /shalat [Lokasi]'
										          ),
										          1 => 
										          array (
													'type' => 'postback',
													'label' => 'Cari Sinopsis Film',
													'data' => 'action=add&itemid=111',
													'text' => 'Ketik /cuaca [Lokasi]'
										          ),
										          2 => 
										          array (
													'type' => 'uri',
													'label' => 'Admin',
													'data' => 'action=add&itemid=111',
													'uri' => 'https://bit.ly/2J3ywc3'
										          ),
										        ),
										      ),											  
										    ),
										  ),
										)					
			 
        )
    );
	}
	
}
if (isset($balas)) {
    $result = json_encode($balas);
//$result = ob_get_clean();
    file_put_contents('./balasan.json', $result);
    $client->replyMessage($balas);
}?>
