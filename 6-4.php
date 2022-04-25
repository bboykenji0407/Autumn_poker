<?php
ini_set( 'display_errors', 1 );
ini_set( 'error_reporting', E_ALL ); 

session_start();
$dsn = 'mysql:dbname=Autumn_curriculum;host=localhost;charset=utf8';
$pdo = new PDO($dsn, 'root', 'root');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
try {
    $dsn = new PDO($dsn, 'root', 'root');
} catch (PDOException $e) {
    $msg = $e->getMessage();
}
$sql = 'SELECT * FROM trumps';
$stmt = $dsn->prepare($sql);
$stmt->execute();
$cards = $stmt->fetchAll();
//print_r($cards);


Class pokerClass{
    
    public function createCards()
    {
        //変数の初期化
        $newCard = array();
        $marks = array();
        //山札を作成
        $card = array();
        for($mark = 0; $mark < 4; $mark++) {
            for($num = 1; $num <= 13; $num++) {    
                $newCard["mark"] = $marks[$mark];
                $newCard["number"] = $num;
                array_push($card,$newCard);
            }
        }
        return $card;
    }
    // 山札（データベース）から、自分と相手に5枚ずつカードを渡す。
    public function shuffleCards($cards)
    {
        //山札をランダムに取り出す
        shuffle($cards); 
        //各プレイヤー山札から5枚引く
        $prayerOne = array();
        $prayerTwe = array();
        for($i = 0; $i < 5; $i++) {
            $prayerOne[] = array_pop($cards);
        }
        for($i = 0;$i < 5; $i++){
            $prayerTwe[] = array_pop($cards);
        }
        return array($prayerOne,$prayerTwe);
    }
    
    // 手札の表示
    public function showCards($cards)
    {
        foreach($cards as $card) {
            $mark = $card['mark'];
            $number = $card['number'];
            if ($mark === 'diamond') {
                echo '🔸';
            } elseif ($mark === 'club') {
                echo '☘️';
            } elseif ($mark === 'spade') {
                echo '♠️';
            } elseif ($mark === 'heart') {
                echo '❤️';
            } else {
                echo 'ジョーカー';
            } 
            echo  $number;
        }
    }
}

$trump = new pokerClass();
$prayersCard = $trump->shuffleCards($cards);
$prayerOne[] = $trump->shuffleCards($prayersCard[0]);
sleep(1);
// 判定
// $role = $trump->role($prayersCard[0]);
//役判定

// HTMLに場所ごとに表示したくて一枚ずつ取り出してみた
$myCardsOneMark = $prayerOne[0][0][0]['mark'];
$myCardsOneNumber = $prayerOne[0][0][0]['number'];
$myCardsTweMark = $prayerOne[0][0][1]['mark'];
$myCardsTweNumber = $prayerOne[0][0][1]['number'];
$myCardsThreeMark = $prayerOne[0][0][2]['mark'];
$myCardsThreeNumber = $prayerOne[0][0][2]['number'];
$myCardsFourMark = $prayerOne[0][0][3]['mark'];
$myCardsFourNumber = $prayerOne[0][0][3]['number'];
$myCardsFiveMark = $prayerOne[0][0][4]['mark'];
$myCardsFiveNumber = $prayerOne[0][0][4]['number'];
if ($myCardsOneMark === 'diamond') {
    $myCardsOneMark = '🔸';
} elseif ($myCardsOneMark === 'club') {
    $myCardsOneMark = '☘️';
} elseif ($myCardsOneMark === 'spade') {
    $myCardsOneMark = '♠️';
} elseif ($myCardsOneMark === 'heart') {
    $myCardsOneMark = '❤️';
} 

if ($myCardsTweMark === 'diamond') {
    $myCardsTweMark = '🔸';
} elseif ($myCardsTweMark === 'club') {
    $myCardsTweMark = '☘️';
} elseif ($myCardsTweMark === 'spade') {
    $myCardsTweMark = '♠️';
} elseif ($myCardsTweMark === 'heart') {
    $myCardsTweMark = '❤️';
} 

if ($myCardsThreeMark === 'diamond') {
    $myCardsThreeMark = '🔸';
} elseif ($myCardsThreeMark === 'club') {
    $myCardsThreeMark = '☘️';
} elseif ($myCardsThreeMark === 'spade') {
    $myCardsThreeMark ='♠️';
} elseif ($myCardsThreeMark === 'heart') {
    $myCardsThreeMark = '❤️';
} 

if ($myCardsFourMark === 'diamond') {
    $myCardsFourMark = '🔸';
} elseif ($myCardsFourMark === 'club') {
    $myCardsFourMark = '☘️';
} elseif ($myCardsFourMark === 'spade') {
    $myCardsFourMark = '♠️';
} elseif ($myCardsFourMark === 'heart') {
    $myCardsFourMark = '❤️';
} 

if ($myCardsFiveMark === 'diamond') {
    $myCardsFiveMark = '🔸';
} elseif ($myCardsFiveMark === 'club') {
    $myCardsFiveMark = '☘️';
} elseif ($myCardsFiveMark === 'spade') {
    $myCardsFiveMark = '♠️';
} elseif ($myCardsFiveMark === 'heart') {
    $myCardsFiveMark = '❤️';
} 

// prayerTwe出力
echo "<div class='prayerTwe_cards'>\n";
echo '相手'. '<br />';
$trump->showCards($prayersCard[1]);
echo "</div>";
?>
<!doctype html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ポーカーゲームを実装！</title>
    <!-- MDB -->
    <link
            href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/3.6.0/mdb.min.css"
            rel="stylesheet"
    />
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</head>
<body>
<div class="card text-center w-75 d-flex m-auto mt-3">
    <div class="card-header bg-info fs-4 text-light">ポーカーゲーム</div>
    <div class="card-body">
        <h5 class="card-title">あなたの手札</h5>
        <!-- 手札 -->
        <div class="px-5 py-4 d-flex justify-content-between">
            <div class="card p-3" style="width:8rem; height:12rem;">
                
                <h4 class="card-title"><?php echo $myCardsOneMark; ?></h4>
                <div style="height:2rem;"></div>
                <h3 class="card-text"><?php echo $myCardsOneNumber; ?></h3>
            </div>
            <div class="card p-3" style="width:8rem; height:12rem;">
                <h4 class="card-title"><?php echo $myCardsTweMark; ?></h4>
                <div style="height:2rem;"></div>
                <h3 class="card-text"><?php echo $myCardsTweNumber; ?></h3>
            </div>
            <div class="card p-3" style="width:8rem; height:12rem;">
                <h4 class="card-title"><?php echo $myCardsThreeMark; ?></h4>
                <div style="height:2rem;"></div>
                <h3 class="card-text"><?php echo $myCardsThreeNumber; ?></h3>
            </div>
            <div class="card p-3" style="width:8rem; height:12rem;">
                <h4 class="card-title"><?php echo $myCardsFourMark; ?></h4>
                <div style="height:2rem;"></div>
                <h3 class="card-text"><?php echo $myCardsFourNumber; ?></h3>
            </div>
            <div class="card p-3" style="width:8rem; height:12rem;">
                <h4 class="card-title"><?php echo $myCardsFiveMark; ?></h4>
                <div style="height:2rem;"></div>
                <h3 class="card-text"><?php echo $myCardsFiveNumber; ?></h3>
            </div>
        </div>
        
        <!-- ここまで -->
        <div class="d-flex align-items-center justify-content-between m-auto" style="width:20%;">
            <form method="GET" name="shuffle_card" action="6-4.php">
                <input class="btn btn-warning text-light" type="submit" value="シャッフル">
            </form>
            <form method="POST" name="judge_game" action="6-4.php">
                <input class="btn btn-primary" type="submit" value="勝負！">
            </form>
        </div>
        <p class="fs-10 pt-3">勝敗結果</p>
        <p class="fs-3 text-danger pb-3">
            You Win !!
        </p>
        <p class="card-text">
        <p>【　役による序列　】：ワンペア &lt; ツーペア &lt; スリーカード &lt; フォーカード &lt; ファイブカード</p>
        <p class="pt-3">【　マークによる序列　】：クラブ &lt; ダイヤ &lt; ハート &lt; スペード</p>

        </p>
    </div>
    <div class="card-footer text-muted d-flex justify-content-between align-items-center bg-light">
        <div class="d-flex">
            <div class="mx-3">勝った回数：10回</div>
            <div class="mx-3">負けた回数：4回</div>
        </div>
        <div>
            <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#confirmModal">
                リセット
            </button>
            <!-- Modal -->
            <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="confirmModalLabel">勝敗履歴のリセット</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <pre style="white-space: pre-line; font-size:16px;">
                                勝敗履歴をリセットします。
                                よろしいですか？
                            </pre>
                            <div class="w-50 m-auto d-flex align-items-center justify-content-between">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">前に戻る</button>
                                <button type="button" class="btn btn-danger">リセット</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>

<?php
// 必須機能
// 山札（データベース）から、自分と相手に5枚ずつカードを渡す。
// 勝負！ボタンを押したら強弱を判別し、勝ったらYou Win、負けたらYou Loseを表示する。
// 勝敗数をカウントし、一番下に表示しておく
// ※データベースの設定は個人で行う課題です。
// リセットボタンを押したら勝敗履歴がリセットされる

// 役は、ワンペア、ツーペア、スリーカード、フォーカードで戦う。


