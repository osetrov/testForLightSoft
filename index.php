<?php
/**
 * User: Pavel Osetrov
 * Date: 28.01.14
 * Time: 10:29
 */

/*
 * Класс стека, взял готовое решение в интернете
 */
class ReadingList
{
    protected $stack;
    protected $limit;

    public function __construct($limit = 10) {
        $this->stack = array();
        $this->limit = $limit;
    }

    public function push($item) {
        if (count($this->stack) < $this->limit) {
            array_unshift($this->stack, $item);
        } else {
            return false;
        }
    }

    public function pop() {
        if ($this->isEmpty()) {
            return true;
        } else {
            return array_shift($this->stack);
        }
    }

    public function top() {
        return current($this->stack);
    }

    public function isEmpty() {
        return empty($this->stack);
    }
}

/*
 * Проверка корректности строки для 1 задания
 */
function isCorrect($str) {
    $len = strlen($str);
    if ($len == 0) {
        return true;
    }
    $stack = new ReadingList(strlen($str) + 1);

    for ($i = 0; $i < $len; $i++) {
        if ($i > 0) {
            switch ($str[$i]) {
                case ')':
                    if ($stack->pop() != '(') {
                        $stack->push('(');
                        $stack->push($str[$i]);
                    }
                    break;
                case '}':
                    if ($stack->pop() != '{') {
                        $stack->push('{');
                        $stack->push(($str[$i]));
                    }
                    break;
                default:
                    $stack->push($str[$i]);
                    break;
            }
        }
    }
    if ($stack->pop() === true) {
        return false;
    }
    return true;
}

/**
 * Class Product Класс продукта
 */
class Product {
    private $name;
    private $price = 0;
    private $count = 0;
    private $amount = 0;

    /**
     *  @param $name string Название продукта
     *  @param $price float Цена продукта
     */
    public function __construct($name, $price) {
        $this->name = $name;
        $this->price = (float)$price;
    }

    /**
     * @return string получить название
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return float получить цену
     */
    public function getPrice() {
        return $this->price;
    }

    /**
     * Увеличить количество на 1 и сумму
     */
    public function incCount() {
        $this->count++;
        $this->amount += $this->getPrice();
    }

    /**
     * @param $discount float минус скидка
     */
    public function minus($discount) {
        $this->amount = $this->amount * ((100 - $discount)/100);
    }
}

/**
 * Class Discount_ProductSet класс скидки
 * Здесь резонне было создать дочерние классы и переопределить методы задания скидки, выполнить не успел
 */
class Discount_ProductSet {
    private $productSet = array();
    private $discount = 0;
    private $type = 0;
    private $list = array();
    private $any = 0;
    private $anyExceptions = array();

    /**
     * @param $name string название фунции
     * @param array $params параметры
     */
    public function __call($name, array $params)
    {
        switch($name) {
            case 'setProductSet':
                $this->productSet = $params;
                $this->type = 0;
                break;
            case 'inList':
                $this->productSet = $params[0];
                $this->list       = $params[1];
                $this->type       = 1;
                break;
            case 'setAny':
                $this->any = $params[0];
                $this->anyExceptions = $params[1];
                $this->type = 2;
                break;
        }

    }

    /**
     * @return int получить тип скидки
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @param $discount float изменить скидку
     */
    public function setDiscount($discount) {
        $this->discount = (float)$discount;
    }

    /**
     * @return float получить скидку
     */
    public function getDiscount() {
        return $this->discount;
    }

    /**
     * @return array|Product получить список продуктов
     */
    public function getProductSet() {
        return $this->productSet;
    }

    /**
     * @return array получить список продуктов
     */
    public function getList() {
        return $this->list;
    }

    /**
     * @return int получить количество
     */
    public function getAny() {
        return $this->any;
    }

    /**
     * @return array получить список исключений
     */
    public function getAnyExceptions() {
        return $this->anyExceptions;
    }
}

/**
 * Class Order класс заказа
 */
class Order {
    private $myOrder = array();

    /**
     * @param Product $product добавить продукт
     */
    public function push(Product $product) {
        $key = array_search($product, $this->myOrder);
        if ($key !== false) {
            $this->myOrder[$key]->incCount();
        } else {
            $product->incCount();
            $this->myOrder[] = $product;
        }
    }

    /**
     * @return array получить заказ
     */
    public function getOrder() {
        return $this->myOrder;
    }

    /**
     * @param Product $product продукт
     * @return bool существует ли в заказе продукт
     */
    public function inOrder(Product $product) {
        foreach ($this->myOrder as $itemOrder) {
            if ($itemOrder->getName() == $product->getName()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Рассчитать скидку
     * @param Product $product продукт
     * @param float $discount скидка
     */
    public function calcDiscont(Product $product, $discount) {
        foreach ($this->myOrder as &$itemOrder) {
            if ($itemOrder->getName() == $product->getName()) {
                $itemOrder->minus($discount);
            }
        }
    }

    /**
     * Расчитать количество продуктов с исключениями
     * @param array $exceptions массив с исключениями
     * @return int количество продуктов
     */
    public function getCountProductsWithOutExceptions(array $exceptions) {
        $count = count($this->myOrder);

        foreach ($this->myOrder as $product) {
            foreach ($exceptions as $exc) {
                if ($product->getName() == $exc->getName()) {
                    $count--;
                }
            }
        }
        return $count;
    }

    /**
     * Получить заказ с исключениями
     * @param array $exceptions массив с исключениями
     * @return array массив с продуктами
     */
    public function getOrderWithOutExceptions(array $exceptions) {
        $result = array();

        foreach ($this->myOrder as $product) {
            $exist = false;
            foreach ($exceptions as $exc) {
                if ($product->getName() == $exc->getName()) {
                    $exist = true;
                }
            }
            if (!$exist) {
                $result[] = $product;
            }
        }
        return $result;
    }

}

/**
 * Class Discount_Manager класс управлениями скидками
 */
class Discount_Manager {
    private $myDM = array();
    private $i = 0;

    /**
     * Добавить скидку
     * @param Discount_ProductSet $discount скидка
     */
    public function add(Discount_ProductSet $discount) {
        $this->myDM[] = $discount;
    }

    /**
     * Выдернуть скидку из стека
     * @return bool
     */
    public function pop() {
        if ($this->isEmpty()) {
            return false;
        } else {
            $result = $this->myDM[$this->i];
            unset($this->myDM[$this->i]);
            $this->i++;
            return $result;
        }
    }

    /**
     * Проверить существования
     * @return bool
     */
    public function isEmpty() {
        return empty($this->myDM);
    }
}

/**
 * Class Calculator класс расчета скидок
 */
class Calculator {
    private $order;
    private $dm;

    /**
     * Задать заказ
     * @param Order $productOrder
     */
    public function setOrder(Order $productOrder) {
        $this->order = $productOrder;
    }

    /**
     * Задать менеджер скидок
     * @param Discount_Manager $discountManager
     */
    public function setDiscountManager(Discount_Manager $discountManager) {
        $this->dm = $discountManager;
    }

    /**
     * Рассчитать скидки
     * @return mixed
     */
    public function doCalculation() {
        $received = array();
        while ($discount = $this->dm->pop()) {

            $exist = true;
            switch ($discount->getType()) {
                case 0:
                    $productSet = $discount->getProductSet();
                    foreach ($productSet as $product) {
                        $exist = $exist && $this->order->inOrder($product);
                    }
                    break;
                case 1:
                    $productSet = $discount->getProductSet();
                    $list = $discount->getList();

                    $existA = true;
                    $existA = $existA && $this->order->inOrder($productSet);

                    $existList = true;
                    foreach ($list as $product) {
                        $existList = $existList && $this->order->inOrder($product);
                    }

                    $exist = $existA && $existList;

                    if ($exist) {
                        $productSet = array_merge(array($productSet), $list);
                    }

                    break;
                case 2:
                    $count = $this->order->getCountProductsWithOutExceptions($discount->getAnyExceptions());
                    if ($count > $discount->getAny) {
                        $productSet = $this->order->getOrderWithOutExceptions($discount->getAnyExceptions());
                        $exist = true;
                    }
                    break;
                default:
                    $exist = false;
                    break;
            }


            if ($exist) {
                foreach ($productSet as $product) {
                    if (!in_array($product->getName(), $received)) {
                        $this->order->calcDiscont($product, $discount->getDiscount());
                        $received[] = $product->getName();
                    }
                }
            }
        }
        return $this->order->getOrder();
    }
}



/* 1 задание
    имеется строка с двумя типами скобок, все условия должны выполнятся, каждая открытая скобка должна быть закрыта
    в правильном порядке
*/

assert(isCorrect('') === true);
assert(isCorrect('()') === true);
assert(isCorrect('{()}') === true);
assert(isCorrect('{()}{}') === true);
assert(isCorrect('(())') === true);
assert(isCorrect('{({({({()})})})}') === true);
assert(isCorrect('{(})') === false);

/*
 * 2 задание
 * Создать класс продуктов, корзины и системы скидок
 */

$objectA = new Product('A', 1000);
$objectB = new Product('B', 100);
$objectC = new Product('C', 50);
$objectD = new Product('D', 10);
$objectE = new Product('E', 80);
$objectF = new Product('F', 150);
$objectG = new Product('G', 70);
$objectH = new Product('H', 11);
$objectI = new Product('I', 180);
$objectJ = new Product('J', 88);
$objectK = new Product('K', 90);
$objectL = new Product('L', 45);
$objectM = new Product('M', 80);

$discount1 = new Discount_ProductSet();
$discount1->setProductSet($objectA, $objectB);
$discount1->setDiscount(10);

$discount2 = new Discount_ProductSet();
$discount2->setProductSet($objectD, $objectE);
$discount2->setDiscount(5);

$discount3 = new Discount_ProductSet();
$discount3->setProductSet($objectE, $objectF, $objectG);
$discount3->setDiscount(5);

$discount4 = new Discount_ProductSet();
$discount4->inList($objectA, array($objectK, $objectL, $objectM));
$discount4->setDiscount(5);

$discount5 = new Discount_ProductSet();
$discount5->setAny(3, array($objectA, $objectC));
$discount5->setDiscount(5);

$discount6 = new Discount_ProductSet();
$discount6->setAny(4, array($objectA, $objectC));
$discount6->setDiscount(10);

$discount7 = new Discount_ProductSet();
$discount7->setAny(5, array($objectA, $objectC));
$discount7->setDiscount(20);

$productOrder = new Order();
$productOrder->push($objectA);
$productOrder->push($objectA);
$productOrder->push($objectB);
$productOrder->push($objectC);
$productOrder->push($objectC);
$productOrder->push($objectC);
$productOrder->push($objectD);
$productOrder->push($objectE);
$productOrder->push($objectE);
$productOrder->push($objectF);
$productOrder->push($objectG);
$productOrder->push($objectG);
$productOrder->push($objectG);
$productOrder->push($objectH);
$productOrder->push($objectH);
$productOrder->push($objectH);
$productOrder->push($objectH);
$productOrder->push($objectH);
$productOrder->push($objectI);
$productOrder->push($objectJ);
$productOrder->push($objectK);
$productOrder->push($objectK);
$productOrder->push($objectL);
$productOrder->push($objectL);
$productOrder->push($objectM);

$discountManager = new Discount_Manager();
$discountManager->add($discount1);
$discountManager->add($discount2);
$discountManager->add($discount3);
$discountManager->add($discount4);
$discountManager->add($discount7);
$discountManager->add($discount6);
$discountManager->add($discount5);


$calculator = new Calculator();
$calculator->setOrder( $productOrder );
$calculator->setDiscountManager ($discountManager);

echo "<pre>";
print_r($calculator->doCalculation());
echo "</pre>";