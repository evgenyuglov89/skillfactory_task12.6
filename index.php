<?php
$example_persons_array = [
    [
        'fullname' => 'Иванов Иван Иванович',
        'job' => 'tester',
    ],
    [
        'fullname' => 'Степанова Наталья Степановна',
        'job' => 'frontend-developer',
    ],
    [
        'fullname' => 'Пащенко Владимир Александрович',
        'job' => 'analyst',
    ],
    [
        'fullname' => 'Громов Александр Иванович',
        'job' => 'fullstack-developer',
    ],
    [
        'fullname' => 'Славин Семён Сергеевич',
        'job' => 'analyst',
    ],
    [
        'fullname' => 'Цой Владимир Антонович',
        'job' => 'frontend-developer',
    ],
    [
        'fullname' => 'Быстрая Юлия Сергеевна',
        'job' => 'PR-manager',
    ],
    [
        'fullname' => 'Шматко Антонина Сергеевна',
        'job' => 'HR-manager',
    ],
    [
        'fullname' => 'аль-Хорезми Мухаммад ибн-Муса',
        'job' => 'analyst',
    ],
    [
        'fullname' => 'Бардо Жаклин Фёдоровна',
        'job' => 'android-developer',
    ],
    [
        'fullname' => 'Шварцнегер Арнольд Густавович',
        'job' => 'babysitter',
    ],
];
//Разбиение ФИО
function getPartsFromFullname($fio){
    $arr = explode(' ', $fio);
    return [
        'surname' => $arr[0],
        'name' => $arr[1],
        'patronomyc' => $arr[2],
    ];
}
//Объединение ФИО
function getFullnameFromParts($surname, $name, $patronomyc){
    return $surname . ' ' . $name . ' ' . $patronomyc;
}
//Сокращение ФИО
function getShortName($fio){
    $arr = getPartsFromFullname($fio);
    return $arr['surname'] . ' ' . mb_substr($arr['name'], 0, 1) . '.';
}
//Функция определения пола по ФИО
function getGenderFromName($fio){
    $gender = 0;
    $arr = getPartsFromFullname($fio);
    if(mb_substr($arr['patronomyc'], -3) == 'вна' || mb_substr($arr['name'], -1) == 'а' || mb_substr($arr['surname'], -2) == 'ва'){
        $gender--;
    }else if(mb_substr($arr['patronomyc'], -2) == 'ич' || in_array(mb_substr($arr['name'], -1), ["й" , "н"]) || mb_substr($arr['surname'], -1) == 'в'){
        $gender++;
    }
    return $gender <=> 0;
}
//Определение возрастно-полового состава
function getGenderDescription ($persons_arr){
    $men = array_filter($persons_arr, function($person){
        return getGenderFromName($person['fullname']) == 1;
    });
    $women = array_filter($persons_arr, function($person){
        return getGenderFromName($person['fullname']) == -1;
    });
    $not_determined = array_filter($persons_arr, function($person){
        return getGenderFromName($person['fullname']) == 0;
    });

    $men_percent = round(count($men) / count($persons_arr) * 100, 2);
    $women_percent = round(count($women) / count($persons_arr) * 100, 2);
    $not_determined_percent = round(count($not_determined) / count($persons_arr) * 100, 2);
    $result = <<<MYRESULT
    Гендерный состав аудитории:
    ---------------------------
    Мужчины - $men_percent%
    Женщины - $women_percent%
    Не удалось определить - $not_determined_percent%
    MYRESULT;

    $result = nl2br($result);
    
    return $result;
}
//Идеальный подбор пары
function getPerfectPartner ($surname, $name, $patronomyc, $persons_arr){
    $surname = mb_convert_case($surname, MB_CASE_TITLE_SIMPLE);
    $name = mb_convert_case($name, MB_CASE_TITLE_SIMPLE);
    $patronomyc = mb_convert_case($patronomyc, MB_CASE_TITLE_SIMPLE);
    $full_name = getFullnameFromParts($surname, $name, $patronomyc);
    $gender = getGenderFromName($full_name);
    while(true){
        $random_person = array_rand($persons_arr, 1);
        if(getGenderFromName($persons_arr[$random_person]['fullname']) == -$gender){
            break;
        }
    }
    $str = getShortName($full_name) . ' + ' . getShortName($persons_arr[$random_person]['fullname']) .' = <br>';
    $str .= '&#9825 Идеально на ' . 50 + rand( 0, (100 - 50) * 100) / 100 . '% &#9825';

    return $str;
}


echo 'Пример<br><br>';
$random_person = array_rand($example_persons_array, 1);
$item = $example_persons_array[$random_person];
$arr = getPartsFromFullname($item['fullname']);
echo 'ФИО: ';
echo getFullnameFromParts($arr['surname'], $arr['name'], $arr['patronomyc']) . '<br>';
echo 'Сокращенный вариант: ';
echo getShortName($item['fullname']) . '<br>';
echo 'Пол: ';
$gender = getGenderFromName($item['fullname']);
if($gender > 0){
    echo 'мужской<br>';
}elseif($gender < 0){
    echo 'женский<br>';
}else{
    echo 'не удалось определить<br>';
}
echo '<br>';

echo '**************************************************************<br>';
echo getGenderDescription($example_persons_array) . '<br>';

echo '**************************************************************<br>';
echo 'Идеальный подбор пары<br><br>';
echo getPerfectPartner('ИванОв', 'ИвАН', 'ИваНович', $example_persons_array);







