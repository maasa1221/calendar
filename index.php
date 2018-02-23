<?php
// 現在の年月を取得
$ym = date("Y-m");  
if(isset($_GET['ym'])) {
    $ym = $_GET['ym'];
}
$timeStamp = strtotime($ym . "-01");
if ($timeStamp === false) {
    $timeStamp = time();
}

$file_name = "data/$ymd.txt";
if (file_exists($file_name)) {
    $schedule = file_get_contents($file_name);
} else {
    $schedule = "";
}

    function scheduleCalendarTable($ym, $timeStamp)
    {
        $weekArray = array('日', '月', '火', '水', '木', '金', '土');
        $shukujituClass = '';

        $scheduleCalendar = '<table id="calendarTable">';

        //今月、来月
        $prev = date("Y-m", mktime(0, 0, 0, date("m", $timeStamp) - 1, 1, date("Y", $timeStamp)));
        $next = date("Y-m", mktime(0, 0, 0, date("m", $timeStamp) + 1, 1, date("Y", $timeStamp)));
        $dspPrev = '<a href="?ym=' . $prev . '">&laquo;</a>';//前月へのナビ
        //表示非表示
        if ((strtotime($prev . '-01') < strtotime(date("Y-m-01", mktime(0, 0, 0, date("m") - 12, 1, date("Y")))))){
            $dspPrev = '';
        }
        $dspNext = '<a href="?ym=' . $next . '">&raquo;</a>';//翌月へのナビ
        if (strtotime($next . '-01') > strtotime(date("Y-m-01", mktime(0, 0, 0, date("m") + 12, 1, date("Y"))))) {
            $dspNext = '';
        }
        $scheduleCalendar .= '
  　<tr><th class="calendarHeader">' . $dspPrev . '</th><th colspan="5" class="calendarHeader">' . date("Y", $timeStamp) . "年" . date("n", $timeStamp) . "月" . '</th><th class="calendarHeader">' . $dspNext . '</th></tr>
    <tr><th class="youbi_0">' . $weekArray[0] . '</th><th>' . $weekArray[1] . '</th><th>' . $weekArray[2] . '</th><th>' . $weekArray[3] . '</th><th>' . $weekArray[4] . '</th><th>' . $weekArray[5] . '</th><th class="youbi_6">' . $weekArray[6] . '</th></tr>
    <tr>';

        //月末
        $lastDay = date("t", $timeStamp);

        //1日の曜日
        $youbi = date("w", mktime(0, 0, 0, date("m", $timeStamp), 1, date("Y", $timeStamp)));

        //最終日の曜日
        $lastYoubi = date("w", mktime(0, 0, 0, date("m", $timeStamp) + 1, 0, date("Y", $timeStamp)));

        $scheduleCalendar .= str_repeat('<td></td>', $youbi);

        for ($day = 1; $day <= $lastDay; $day++, $youbi++) {
            $ymd="$ym-$day";
            $file_name = "data/{$ymd}.txt";
            if (file_exists($file_name)) {
                $schedule = file_get_contents($file_name);
            } else {
                $schedule = "";
            }

            $scheduleCalendar .= sprintf('<td class="youbi_%d'.$shukujituClass.'"><a href="schedule.php?ymd=' . $ymd. '">%d<br><textarea rows="10" cols="50" name="schedule" style="width: 50px
; height: 50px;">%s</textarea></a></td>',$youbi % 7, $day, $schedule);
            //土曜で行を変える
            if ($youbi % 7 == 6) {
                $scheduleCalendar .= "</tr><tr>";
            }
            //最終日以降空セル埋め
            if ($day == $lastDay) {
                $scheduleCalendar .= str_repeat('<td class="blankCell"></td>', (6 - $lastYoubi));
            }
        }
        $scheduleCalendar .= "</tr>\n";
        $scheduleCalendar .= "</table>\n";
        //何もない行を削除する。
        $scheduleCalendar = str_replace('<tr></tr>', '', $scheduleCalendar);
        return $scheduleCalendar;
    }

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <title>カレンダー</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="main.css">
</head>
<body>
<?php echo scheduleCalendarTable($ym, $timeStamp); ?>
</body>
</html>


