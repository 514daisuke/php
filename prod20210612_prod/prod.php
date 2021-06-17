<?php
// 出力用の空の文字列
$str = '';
$array = [];
// ファイルを開く(読み取り専用) 
$file = fopen('./data/member.csv', 'r');
// ファイルをロック
flock($file, LOCK_EX);


if ($file) {
    // fgets()で1行ずつ取得→$lineに格納
    // while ($line = fgetcsv($file)) {


    while (($line  = fgetcsv($file, 1000, ",", '"')) !== FALSE) {
        // 取得したデータを$strに入れる }
        $str .= "<tr><td>{$line}</td><tr>";
        // 配列
        array_push($array, $line);
    }
    // var_dump($array2);

    $jsonArray = json_encode($array);
    // ロック解除 fclose($file); // ファイル閉じる
    flock($file, LOCK_UN);
    // ($strに全部の情報が入る!)
}

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha256-3edrmyuQ0w65f8gfBsqowzjJe2iM6n0nKciPUp8y+7E=" crossorigin="anonymous"></script>
    <script src="https://www.amcharts.com/lib/4/core.js"></script>
    <script src="https://www.amcharts.com/lib/4/charts.js"></script>
    <script src="https://www.amcharts.com/lib/4/plugins/wordCloud.js"></script>
    <script src="https://www.amcharts.com/lib/4/themes/animated.js"></script>

    <h1>csvファイル書き込み課題</h1>
</head>

<body>
    <fieldset>
        <form action="create.php" method="post">
            <legend class="title">頻出単語表示リスト</legend>
            <!-- <a href="prod.php">一覧画面</a> -->
            <div>
                <div class="text">name:
                    <div class="input_area"><input type="text" name="name" size="20" style="width:150px;" placeholder="小石大介"></div>
                </div>

                <div class="text">year:
                    <div class="input_area2"> <input type="number" name="year" size="20" style="width:150px;" placeholder="31"></div>
                </div>
                <div class="text">email:
                    <div class="input_area3"><input type="email" name="email" size="20" style="width:150px;" placeholder=" example@cheese.ac.jp"></div>
                </div>
                <!-- <div>
                    question: <input type="text" name="text" size="20" style="width:250px;" placeholder="初めて買ったペットは？">
                </div> -->
                <div>
                    question: <input type="text" name="text" size="20" style="width:250px;" placeholder="今までの講義でつらかったことは？">
                </div>
                <div>
                    <button>submit</button>
                </div>
            </div>
        </form>
        <!-- 課題のプラスアルファ（そのままグラフに変更したので今回はオミット -->
        <!-- <table>
            <tbody>
                <?= $str ?>
            </tbody>
        </table> -->
        <section class="section">

            <!-- /* グラフの描画先 */ -->
            <div id="chartdiv" class="chartdiv"></div>
            </div>

        </section>
    </fieldset>
</body>
<style>
    .h1 {
        /*文字色*/
        color: #364e96;
        /*上下の余白*/
        padding: 0.5em 0;
        /*上線*/
        border-top: solid 3px #364e96;
        /*下線*/
        border-bottom: solid 3px #364e96;
    }


    .title {
        /*文字色*/
        color: #505050;
        /*文字周りの余白*/
        padding: 0.5em;
        /*行高*/
        display: inline-block;
        /*背景色*/
        background: #dbebf8;
        vertical-align: middle;
        /*左側の角を丸く*/
        border-radius: 25px 0px 0px 25px;
    }

    .chartdiv {
        width: 100%;
        height: 500px;
    }

    .text {
        display: flex;
        flex-direction: row;
    }

    .input_area {
        padding-left: 29px;
    }

    .input_area2 {
        padding-left: 38px;
    }

    .input_area3 {
        padding-left: 33px;
    }
</style>


<script>
    // console.log("JS確認")
    var json_data = '<?php echo json_encode($array); ?>';

    // 受け取った値をコンソール出力
    // console.log(json_data);

    var result = [];
    // const replaced = json_data.replace(',', ' ')
    // console.log(replaced);

    result = json_data.split(',')
    data = json_data.split('"\"')
    // str.join(',')
    console.log(result);
    console.log(data);

    $(window).on('load', function() {
        loadText();
    });


    // テキストグラフの作成
    function loadText() {
        // テキストデータ
        // var sentence = $('textarea[name="text"]').val();
        var sentence = json_data;


        // 改行を半角スペースに変換
        sentence = sentence.replace(/\r?\n/g, ' ');
        // グラフ描画
        drawWorldCloud(sentence);
    }


    function drawWorldCloud(sentence) {
        // アニメーションテーマを使う
        am4core.useTheme(am4themes_animated);

        var chart = am4core.create("chartdiv", am4plugins_wordCloud.WordCloud);
        var series = chart.series.push(new am4plugins_wordCloud.WordCloudSeries());

        series.accuracy = 4;
        series.step = 15;
        series.rotationThreshold = 0.7;
        series.maxCount = 200;
        series.minWordLength = 2; // 最少頻度
        series.labels.template.tooltipText = "{word}: {value}";
        series.fontFamily = "Courier New";
        series.maxFontSize = am4core.percent(30);

        // 文字列を渡すだけ
        series.text = sentence;

        // カラフルになる。
        series.colors = new am4core.ColorSet();
        series.colors.passOptions = {}; // makes it loop

        // 配置が動くようになる
        setInterval(function() {
            series.dataItems.getIndex(Math.round(Math.random() * (series.dataItems.length - 1))).setValue("value", Math.round(Math.random() * 10));
        }, 10000)
    }
</script>

</html>