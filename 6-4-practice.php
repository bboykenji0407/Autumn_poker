<!DOCTYPE html>
<html>
<head>
    <title>占い</title>
</head>
<body style="text-align:center;">
    <h1>今日の運勢<h1>
    <?php
        if(isset($_POST['button'])){
            $arr =array("大吉","中吉","小吉","吉","末吉","区","大区");
            $key = array_rand($arr, 1);
            echo ($arr[$key]);
        }
    ?>
    <form method="post">
        <input type="submit" name="button" value="占う"/>
    </form>
    <?php
        if(isset($_POST['shuffle_card'])){
            $arr =array("大吉","中吉","小吉","吉","末吉","区","大区");
            $key = array_rand($arr, 1);
            echo ($arr[$key]);
        }
    ?>
    <form method="POST" name="shuffle_card" action="6-4-practice.php">
        <input class="btn btn-warning text-light" type="submit" value="シャッフル">
     </form>
    </body>    
</html>

<?php
// testJudge();
exit;
// 初期化
$status = array();

$cards = initCards();

sleep(1);
// 判定
$judge = judge($drowCards);

//////////
// ここから下は関数群
//////////

/**
 * カードを五枚引く
 * カードの番号を指定すると引きたいカードが引ける
 */
function drow($cards, $cardNums = array()) {
  $rand = array();
  if (! $cardNums) {
    while(true) {
      $rand[] = rand(0, 51);
      $rand = array_unique($rand);
      if (count($rand) == 5) {
	break;
      }
    }
  } else {
    $rand = $cardNums;
  }
  $result = array();
  foreach ($rand as $i) {
    $result[] = $cards[$i];
  }
  return $result;
}
/**
 * トランプの初期化
 */
function initCards() 
{
  $cards = array();
  $marks = array("&#9826;", "&#9825;", "&#9828;", "&#9831;");
  $numbers = range(1, 13);
  $dispNumbers = array("Ａ", '２', "３", "４", "５", "６", "７", "８", "９", "10", "Ｊ", "Ｑ", "Ｋ");
  foreach ($marks as $mark) {
    foreach ($numbers as $number) {
      $cards[] = array("number"=> $number,
		       "dispNumber" => $dispNumbers[$number-1],
		       "mark"  => $mark);
    }
  }
  return $cards;
}

/**
 * カードを５枚表示する
 */
function displayCards($cards)
{
  $disp = "";
  foreach ($cards as $card) {
    $disp .= displayCard($card);
  }
  return $disp;
}
/**
 * カードを表示する
 */
function displayCard($card)
{
  $disp = "";
  $disp .= "------\n";
  $disp .= "| {$card['dispNumber']} |\n";
  $disp .= "| {$card['mark']} |\n";
  $disp .= "------\n";
  return $disp;
}

////////////////////
// カードの判定のための関数
////////////////////
/**
 * カードの判定
 */
function judge($cards) {
  $state = 0;

  // フォーカード
  if (isFour($cards)) {
    return "four";
  }

  // スリーカード
  if (isThree($cards)) {
    return "three";
  }

  // ツーペア
  if (isPair($cards)) {
    return "tow";
  }
  return "";
}

/**
 * フォーカードか調べる
 */
function isFour($cards)
{
  $state = searchPair($cards);
  rsort($state);
  if (array_shift($state) == 4) {
    return true;
  }
  return false;

}

/**
 * 3cards ?
 */
function isThree($cards)
{
  $state = searchPair($cards);
  rsort($state);
  if (array_shift($state) == 3) {
    return true;
  }
  return false;
}

/**
 * 2pair?
 */
function isPair($cards)
{
  $state = searchPair($cards);
  rsort($state);
  if (array_shift($state) == 2) {
    return true;
  }
  return false;
}

/**
 * マークが一緒か調べる
 */
function isSameMark($cards)
{
  $state = true;
  $mark = "";
  foreach ($cards as $card) {
    if ($mark !== "" && $mark !== $card['mark']) {
      $state = false;
      break;
    }
    $mark = $card['mark'];
  }
  return $state;
}
/**
 * どんなカードが何枚一緒か調べる
 */
function searchPair($cards)
{
  $state = array();
  foreach ($cards as $card) {
    if (! isset($state[$card['number']])) {
      $state[$card['number']] = 0;
    }
    $state[$card["number"]]++;
  }
  return $state;
}

////////
// テスト用の関数
////////
// function testJudge()
// {
//   $testCards = array("royal"         => array(0, 9, 10, 11, 12),
// 		     "straightFlash" => array(7, 8, 9, 10, 11),
// 		     "four"          => array(0, 13, 26, 39, 50),
// 		     "fullHouse"     => array(0, 13, 26, 1, 14),
// 		     "flash"         => array(0, 3, 5, 6, 9),
// 		     "straight"      => array(0, 14, 15, 3, 4, 5),
// 		     "three"         => array(0, 13, 26, 5, 6),
// 		     "tow"           => array(0, 13, 1, 14, 8));
//   foreach ($testCards as $name => $card) {
//     $drow = drow(initCards(), $card);
//     if (judge($drow) == $name) {
//       echo "{$name} is ok\n";
//     } else {
//       echo "{$name} is NG\n";
//     }      
//     echo displayCards($drow);
//   }
// }