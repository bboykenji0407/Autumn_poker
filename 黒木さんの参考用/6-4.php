<?php

/**
 * ポーカーゲームを行うクラス
 * 【シャッフルボタン】手札が切り替わる
 * 【勝負ボタン】自分と相手の手札をもとに勝負する
 * 【リセットボタン】勝敗数がリセットする
 * ◆DBにresults, self_cards, enemy_cardsテーブルを作成
 * ◆resultsテーブルには初期データを登録する必要あり
 */
class Poker
{
    /*
    役の種類
    ・役なし：0      ・ワンペア：1
    ・ツーペア：2     ・スリーカード：3
    ・フォーカード：4 ・ファイブカード：5
    */
    // 自分の役に関する詳細情報
    public $card_self_inf = [
        'role' => 0, // 役
        'number' => '', // 重複しているカードのトランプの数字
        'mark' => [] // 重複しているカードのマーク
    ];
    // 相手の役に関する詳細情報
    public $card_enemy_inf = [
        'role' => 0, // 役
        'number' => [], // 重複しているカードのトランプの数字
        'mark' => [] // 重複しているカードのマーク
    ];
    public $selfCards = []; // 自分の手札
    public $enemyCards = []; // 敵の手札
    public $message = ''; // 勝敗のメッセージ
    public $winCount = 0; // 勝利数をカウント
    public $loseCount = 0; // 敗北数をカウント

    /**
     * ・PDOに接続し、インスタンスを返す
     * @return PDO $dbh PDOインスタンス
     */
    public function createPDO()
    {
        $dsn = 'mysql:dbname=autumn_curriculum;host=127.0.0.1;port=8889;';
        $user = 'root';
        $password = 'root';

        try {
            // PDOインスタンスを作成
            $dbh = new PDO($dsn, $user, $password);
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $dbh;
        } catch (PDOException $e) {
            echo '接続失敗' . $e->getMessage();
            exit();
        }
    }

    /**
     * DBからカード情報を取得
     * @return Array $cards カード情報
     */
    public function setCardInf(PDO $dbh)
    {
        // trumpsテーブルから無作為に10件取得
        $sql = 'select * from trumps order by rand() limit 10';

        try {
            $stmt = $dbh->prepare($sql);
            $stmt->execute();
            $cards_info = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $this->updateCardInf($cards_info, $dbh);
        } catch (PDOException $e) {
            echo '接続失敗' . $e->getMessage();
            exit();
        }
    }

    /**
     * 【シャッフル時に実行】
     * ・self_cards、enemy_cardsのレコードを全削除
     * ・無作為に取得した10枚のカードを、自分と相手の手札に分割（5枚ずつ）
     * ・分割したカード情報はDBに保存
     * @param array $cards_info 無作為に取得したカード10枚
     */
    public function updateCardInf(array $cards_info, PDO $dbh)
    {
        // 初期化
        $this->selfCards = [];
        $this->enemyCards = [];

        // self_cards、enemy_cardsのレコードを全削除
        $sql = 'truncate table self_cards;truncate table enemy_cards;';

        try {
            $stmt = $dbh->prepare($sql);
            $stmt->execute();

            $count = 0; // ループ処理のカウント
            $selfInf = []; // 自分の手札
            $enemyInf = []; // 相手の手札
            // 自分及び敵の手札をセット
            foreach ($cards_info as $card) {
                if ($count < 5) {
                    $card['number'] = intval($card['number']);
                    // sql用のデータを生成
                    array_push($selfInf, "('{$card['mark']}', '{$card['number']}')");
                    // HTML用のカード情報を生成
                    array_push($this->selfCards, $card);
                    // 自分のカードを最大枚数（5枚目）
                    if ($count === 4) {
                        // self_cardsテーブルに、5枚のカード情報を登録
                        $sql = 'insert into self_cards (mark, number) values ' . join(',', $selfInf);
                        $stmt = $dbh->prepare($sql);
                        $stmt->execute();
                    }
                } else {
                    $card['number'] = intval($card['number']);
                    // sql用のデータを生成
                    array_push($enemyInf, "('{$card['mark']}', '{$card['number']}')");
                    // HTML用のカード情報を生成
                    array_push($this->enemyCards, $card);
                    // 相手のカードを最大枚数（10枚目）
                    if ($count === 9) {
                        // enemy_cardsテーブルに、5枚のカード情報を登録
                        $sql = 'insert into enemy_cards (mark, number) values ' . join(',', $enemyInf);
                        $stmt = $dbh->prepare($sql);
                        $stmt->execute();
                    }
                }
                $count++;
            }
        } catch (PDOException $e) {
            echo '接続失敗' . $e->getMessage();
            exit();
        }
    }

    /**
     * 【勝負時に実行】
     * ・シャッフルでDBに保存したカード情報を取得
     * ・取得したカード情報をもとにカード情報を出力
     * @param int $player 識別子（1：自分、2：相手）
     */
    public function getCardInf(int $player, PDO $dbh)
    {
        // 自分もしくは相手の手札かどうかを識別
        if ($player === 1) {
            $sql = 'select * from self_cards';
        } else {
            $sql = 'select * from enemy_cards';
        }

        try {
            $stmt = $dbh->prepare($sql);
            $stmt->execute();
            $cards_info = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // 自分の手札の場合
            if ($player === 1) {
                // 自分の手札情報を生成
                foreach ($cards_info as $card) {
                    $card['number'] = intval($card['number']);
                    array_push($this->selfCards, $card);
                }

                // 上記で生成した情報をもとに、役を判定
                $this->cardJudge($cards_info, 1);
            } else {
                // 相手の手札情報を生成
                foreach ($cards_info as $card) {
                    $card['number'] = intval($card['number']);
                    array_push($this->enemyCards, $card);
                }

                // 上記で生成した情報をもとに、役を判定
                $this->cardJudge($cards_info, 2);
            }
        } catch (PDOException $e) {
            echo '接続失敗' . $e->getMessage();
            exit();
        }
    }

    /**
     * 【シャッフル時、勝負時実行】
     * ・勝った回数及び、負けた回数をDBから取得
     */
    public function getResultInf(PDO $dbh)
    {
        // resultsテーブルから勝敗結果を取得
        $sql = 'select * from results';

        try {
            $stmt = $dbh->prepare($sql);
            $stmt->execute();
            $resultInf = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // 勝った回数及び、負けた回数の情報をセット
            foreach ($resultInf as $result) {
                $this->winCount = $result['win'];
                $this->loseCount = $result['lose'];
            }
        } catch (PDOException $e) {
            echo '接続失敗' . $e->getMessage();
            exit();
        }
    }

    /**
     * 【勝負時に実行】
     * ・勝負した結果をDBに保存
     */
    public function setResult(PDO $dbh)
    {
        // 勝敗結果をアップデートするSQL
        $sql = 'update results set win = :win, lose = :lose where id = 1';

        try {
            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(':win', $this->winCount, PDO::PARAM_INT); // 勝った回数をパラメータに指定
            $stmt->bindValue(':lose', $this->loseCount, PDO::PARAM_INT); // 負けた回数をパラメータに指定
            $stmt->execute();
        } catch (PDOException $e) {
            echo '接続失敗' . $e->getMessage();
            exit();
        }
    }

    /**
     * 【リセット時に実行】
     * ・勝敗数をリセット
     */
    public function resetResult()
    {
        // 勝敗結果をアップデートするSQL
        $sql = 'update results set win = :win, lose = :lose where id = 1';

        try {
            // PDOインスタンスの作成
            $dbh = $this->createPDO();
            $stmt = $dbh->prepare($sql);
            $stmt->bindValue(':win', 0, PDO::PARAM_INT);
            $stmt->bindValue(':lose', 0, PDO::PARAM_INT);
            $stmt->execute();
        } catch (PDOException $e) {
            echo '接続失敗' . $e->getMessage();
            exit();
        }
    }

    /**
     * 【勝負時に実行】
     * ・カードの数字をもとに役を判定
     * ・識別子をもとに、グローバル変数にカード情報をセット
     * @param array $cards 無作為に取得した5枚のカード情報
     * @param int $player 識別子（1：自分、2：相手）
     */
    public function cardJudge(array $cards = [], int $player = null)
    {
        // $card_self_inf, $card_enemy_infのローカル変数
        $player_inf = [
            'role' => 0,
            'number' => '',
            'mark' => []
        ];

        // 手札カードの数字のみを抽出
        $numbers = [];
        foreach ($cards as $card) {
            array_push($numbers, $card['number']);
        }

        // 重複しているカードを取得
        $duplicate_nums = array_count_values($numbers);

        // 重複しているカードを元に役を指定
        foreach ($duplicate_nums as $key => $value) {
            // 重複数が5枚の時
            if ($value === 5) {
                // マーク情報もセット
                $player_inf = $this->setMarkAndNum($key, $cards, $player_inf);
                // 重複数が4枚の時
            } else if ($value === 4) {
                // マーク情報もセット
                $player_inf = $this->setMarkAndNum($key, $cards, $player_inf);
                // 重複数が3枚の時
            } else if ($value === 3) {
                // スリーカードとツーペアが重複した際、役をスリーカードにセット
                $player_inf = $this->hasRole($key, $player_inf, $value);
                // マーク情報もセット
                $player_inf = $this->setMarkAndNum($key, $cards, $player_inf);
                // 重複枚数が2枚以下の時
            } else if ($value === 2) {
                // ツーペアとワンペアを判断し、役をセット
                $player_inf = $this->hasRole($key, $player_inf, $value);
                // マーク情報もセット
                $player_inf = $this->setMarkAndNum($key, $cards, $player_inf);
                // 役なしの時
            }
        }

        // 自分の手札か敵の手札か判断
        if ($player === 1) {
            // 値をリセット
            $this->card_self_inf = [];
            $this->card_self_inf = $player_inf;
        } else if ($player === 2) {
            // 値をリセット
            $this->card_enemy_inf = [];
            $this->card_enemy_inf = $player_inf;
        } else {
            echo '手札が自分もしくは敵のものか判断できません。';
            return;
        }
    }

    /**
     * 【勝負時に実行】
     * ・スリーカード以下の役を判断
     * @param int $num カードの数字
     * @param array $player_inf プレイヤーの役情報
     * @param int $value 役情報（重複枚数：3 or 2）
     * @return array $player_inf プレイヤーの役情報
     */
    public function hasRole(int $num = null, array $player_inf = [], int $value = null)
    {
        // 役がスリーカードの時
        if (intval($player_inf['role']) === 3) {
            return;
            // 役がワンペアかつ、2枚重複したカードが出たの時
        } else if (intval($player_inf['role']) === 1 && $value === 2) {
            $player_inf['role'] = $value;

            // 既存の番号より、2回目の番号の方が大きい場合
            if ($player_inf['number'] <= $num) {
                $player_inf['number'] = $num;
            }
            // 役無しの時
        } else {
            $player_inf['role'] = 1;
            $player_inf['number'] = $num;
        }

        return $player_inf;
    }

    /**
     * 【勝負時に実行】
     * ・マーク情報をセットし、情報を返す
     * @param int $num カードの数字
     * @param array $cards 5枚のカード情報
     * @param array $player_inf プレイヤー役情報
     * @return array $player_inf プレイヤー役情報
     */
    public function setMarkAndNum(int $num = null, array $cards = [], array $player_inf = [])
    {
        // 重複している数字の情報を追加
        $player_inf['number'] = $num;

        // マーク情報を追加
        foreach ($cards as $card) {
            if (intval($card['number']) === $num) {
                array_push($player_inf['mark'], $card['mark']);
            }
        }

        return $player_inf;
    }

    /**
     * 【勝負時】
     * ・自分と相手の役情報を比較し、勝敗を決める
     * ・勝敗の結果によって、適宜メッセージを代入
     * @param array $self 自分の役情報
     * @param array $enemy 相手の役情報
     */
    public function resultJudge(array $self, array $enemy, PDO $dbh)
    {
        // 値のリセット
        $this->message = '';

        // 役柄で勝敗を判定
        // 自分の役が強い時
        if ($self['role'] > $enemy['role']) {
            $this->message = 'You Win!!';
            $this->winCount++;
            // 相手の役が強い時
        } else if ($self['role'] < $enemy['role']) {
            $this->message = 'You Lose!!';
            $this->loseCount++;
            // 引き分けの時
        } else if ($self['role'] === $enemy['role']) {
            // お互い役がない時
            if ($this->isDrow($self, $enemy) === 0) {
                $this->message = 'You Draw!!';
                // 自分がマークで勝利した時
            } else if ($this->isDrow($self, $enemy) === 1) {
                $this->message = 'You Win!!';
                $this->winCount++;
                // 相手がマークで勝利した時
            } else if ($this->isDrow($self, $enemy) === 2) {
                $this->message = 'You Lose!!';
                $this->loseCount++;
                // 上記以外
            } else {
                $this->message = 'Try Again!!';
            }
            // 上記以外の時
        } else {
            $this->message = 'Try Again!!';
        }
    }
}