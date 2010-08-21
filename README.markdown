Mockery
========

Mockery это простой, но тем не менее гибкий PHP mock фреймворк для использования
в модульном тестировании. Вдохновенный фреймворками Ruby's flexmock и Java's Mockito,
вобравший элементы API обоих.

Mockery выпущен под лицензией New BSD License.

Mock Objects
------------

В модульном тестировании, mock-объекты симулируют поведение реальных объектов.
Они обычно используются для изоляции тестов, встают вместо объектов, которых еще
нету, а также позволяют исследовать дизайн API классов, не требуя подлинной реализации.

Преимущества от mock фреймворка заключаются в генерации как раз таких mock-объектов
(и заглушек). Они позволяют установить вызовы ожидаемых методов и возвращаемые
результаты, используя гибкий API, который способен к воплощению поведения каждого
возможного реального объекта, путем описания на естественном языке настолько близко,
насколько это возможно.

Предпосылки
-------------

Mockery требует PHP 5.3 который является необходимым.

Установка
------------

Предпочтительный режим установки через PEAR. Mockery хостится на PEAR канале Survivethedeepend.com:

    pear channel-discover pear.survivethedeepend.com
    pear install deepend/Mockery

Репозиторий git содержит разрабатываемую версию в своей master ветке. Вы можете
установить эту версию используя следующие команды:

    git clone git://github.com/padraic/mockery.git
    cd mockery
    sudo pear install package.xml

Это установит Mockery как PEAR библиотеку.

Простой пример
--------------

Представим мы имеем класс Temperature который производит выборку температуры местности
и предоставляет отчет о средней температуре. Данные могут придти от веб-сервиса или
любого другого источника, но мы не имеем такого класса на данный момент. Тем не
менее, мы можем предположить поведение этого класса, на основании его взаимодействии
с классом Temperature.

    class Temperature
    {

        public function __construct($service)
        {
            $this->_service = $service;
        }

        public function average()
        {
            $total = 0;
            for ($i=0;$i<3;$i++) {
                $total += $this->_service->readTemp();
            }
            return $total/3;
        }

    }

Даже в отсутствии реального касса сервиса, мы можем увидеть ожидаемое его поведение.
Когда мы пишем тест для класса Temperature, мы можем заменить mock-объектом реальный
класс, который позволит нам протестировать поведение класса Temperature фактически
не нуждаясь в конкретном экземпляре сервиса.

Заметка: Интеграция с PHPUnit (смотри ниже) может отменить потребность в методе teardown().

    use \Mockery as m;

    class TemperatureTest extends extends PHPUnit_Framework_TestCase
    {

        public function teardown()
        {
            m::close();
        }

        public function testGetsAverageTemperatureFromThreeServiceReadings()
        {
            $service = m::mock('service');
            $service->shouldReceive('readTemp')->times(3)->andReturn(10, 12, 14);
            $temperature = new Temperature($service);
            $this->assertEquals(12, $temperature->average());
        }

    }

Мы рассмотрим API более детально ниже.

PHPUnit Интеграция
-------------------

Mockery был спроектирован как простой в использовании, независимый объектный
mock-фреймворк, таким образом интеграция с любым фреймворком для тестировании
является опциональной. Для интеграции Mockery вам необходимо всего лишь определить
метод teardown() для ваших тестов, содержащий следующее (вы можете использовать
коротку запись \Mockery путем namespace алиаса):

    public function teardown() {
        \Mockery::close();
    }

Этот статический вызов очищает Mockery контейнер, используемый в текущем тесте
и запускает любые задачи проверки, необходимые для ваших ожиданий.

Для большей краткости, при использовании Mockery, вы можете также явно установить
короткий псевдоним через namespace. Для примера:

    use \Mockery as m;

    class SimpleTest extends extends PHPUnit_Framework_TestCase
    {
        public function testSimpleMock() {
            $mock = m::mock('simple mock');
            $mock->shouldReceive('foo')->with(5, m::any())->once()->andReturn(10);
            $this->assertEquals(10, $mock->foo(5));
        }

        public function teardown() {
            m::close();
        }
    }

Mockery поставляется с автозагрузчиком, т.о. вы не захламляете свои тесты вызовами
require_once(). Для его использования, убедитесь что Mockery в вашем include_path
и добавьте следующий код в Bootstrap вашего тестового окружения или TestHelper файл:

    require_once 'Mockery/Loader.php';
    $loader = new \Mockery\Loader;
    $loader->register();

Справочник
---------------

Mockery реализует краткий API при создании mock. Далее пример возможного метода запуска.

    $mock = \Mockery::mock('foo');

Создаст mock-объект, названный foo. В этом случае, foo - это имя (не обязательно
имя класса), используемое в качестве простого идентификатора при поступлении исключений.
При этом создастся mock-объект типа \Mockery\Mock и это самая свободная возможная форма mock-объекта.

    $mock = \Mockery::mock(array('foo'=>1,'bar'=>2));

Создаст безымянный mock-объект, так как мы не передали имя. Тем не менее, мы
передали массив ожиданий - быстрый способ установки методов, вызовы которых
ожидается и их возвращаемые значения.

    $mock = \Mockery::mock('foo', array('foo'=>1,'bar'=>2));

Подобно предыдущим примерам, только демонстрирует комбинацию установки имени и массива ожиданий.

    $mock = \Mockery::mock('stdClass');

Создаст mock-объект идентичный именованному, за исключением того, что вместо
имени - имя реального класса. Создается простой mock как и в предыдущих примерах,
за исключением того, что mock-объект наследует тип класса, т.е. произойдет
подсветка типа или instanceof вычислится как stdClass. Полезно когда подменяемый
объект должен быть определенного типа.

    $mock = \Mockery::mock('FooInterface');

Вы можете создать mock-объект на базе любого реального класса, абстрактного класса
или даже интерфейса. Повторюсь, основная цель гарантировать что mock-объект
наследует указанный тип для подсветки типа.

    $mock = \Mockery::mock('FooInterface', array('foo'=>1,'bar'=>2));

Да, вы можете использовать ту же самую быструю установку ожиданий как и с
именованными mock-объектами, только использовать имена классов.

    $mock = \Mockery::mock(new Foo);

Передача любого реального объекта в Mockery приведет к созданию частичного mock-объекта.
Частичность предполагает что вы можете создать конкретный объект, таким образом
все что вам необходимо сделать - это выборочно переопределить существующие методы
(или добавить несуществующие) так как вы ожидаете.

    $mock = \Mockery::mock(new Foo, array('foo'=>1));

Вы также можете использовать быструю установку ожиданий для ваших частичных
mock-объектов. Смотрите секцию Создание Частичных Mock-объектов для получения
более подробной информации.

    $mock = \Mockery::mock('name', function($mock){
        $mock->shouldReceive(method_name);
    });

Все из различных установочных методов могут принимать замыкания как последний
параметр. Замыканию передастся mock-объект когда произойдет вызов, таким образом это
ожидание может быть установлено. В отличие от поздних описанных по умолчанию ожиданий,
это позволяет повторные установки ожидания, сохраняя их как замыкание для выполнения.
Заметьте, что все другие параметры, включая ожидания через быстрый набор массивов
будут использованы прежде чем замыкание будет вызвано.

Объявление Ожиданий
------------------------

Однажды, создав mock-объект, вы захотите определить как точно он должен себя
вести (и как он должен вызываться). Эта секция как раз про описание ожиданий.

    shouldReceive(method_name)

Объявляет что mock-объект ожидает вызов данного метода. Это начальная точка
описания ожиданий, на которое добавляются другие ожидания и ограничения.

    shouldReceive(method1, method2, ...)

Объявляет много ожидаемых вызовов методов, все из которых примут по цепочке
любые ожидания или ограничения.

    shouldReceive(array(method1=>1, method2=>2, ...))

Объявляет много ожидаемых вызовов, а так же их возвращаемые значения. Все по
цепочке могут принять любые дополнительные ожидания и ограничения.

    shouldReceive(closure)

Создает mock-объект (только для частичного mock-объекта) который используется
для создания рекордера. Рекордер это простой прокси для исходного объекта
переданный для подмены. Он передается замыканию, который может запустить его
через множество операций, которые рекордер ожидает на частичном mock-объекте.
Простой вариант использования - это автоматическая запись ожиданий основанных на
существующем использовании (например во время рефакторинга). См. пример в
следующей секции.

    with(arg1, arg2, ...)

Добавляет ограничения, которые применяются к вызовам ожидаемых методов как
список аргументов. Вы можете добавить больше гибкости к аргументам используя
встроенный класс (см. дальше). Например, \Mockery::any() соответствует любому
аргументу переданному в этой позиции в  списке параметров with().

Важно отметить, что это означает что все ожидания применяются только к данному
методу, когда он вызывается с этими точными аргументами. Допускается установка
различных ожиданий, основанных на аргументах предоставленных для ожидаемых вызовов.

    withAnyArgs()

Объявляет что это ожидание соответствует вызовам метода независимо от того, какие
параметры были переданы. Такое поведение подразумевается по умолчанию, если
другое не определено.

    withNoArgs()

Объявляет что это ожидание должно вызываться без параметров.

    andReturn(value)

Устанавливает значение, которое вернет вызов ожидаемого метода.

    andReturn(value1, value2, ...)

Устанавливает последовательность возвращаемых значений или замыканий (closures).
Например, первый вызов вернет value1, а второй value2. Но не так, что все
последующие вызовы подменяемого метода будут всегда возвращать последнее
значение установленное в этом объявление.

    andReturnUsing(closure, ...)

Устанавливает замыкание (анонимная функция), которое будет вызвано с параметрами
переданными в метод. Затем, то что вернет замыкание вернется при вызове
подменяемого метода. Полезно для некой динамической обработки параметров,
связанных с конкретным результатом. Замыкания могут образовывать очередь,
передавая их как дополнительные параметры как для andReturn(). Заметьте, что на
данный момент вы не можете смешивать  andReturnUsing() с andReturn().

    andThrow(Exception)

Объявляет что этот метод выбросит данный объект Exception когда будет вызван.

    andThrow(exception_name, message)

Вместо объекта, вы можете передать класс Исключения и сообщение, чтобы использовать
его для выброса Исключения из подменяемого метода.

    zeroOrMoreTimes()

Объявляет что ожидаемый метод может быть вызван ноль или больше раз. Это значение
по умолчанию для всех методов, если не установлено иное.

    once()

Объявляет, что ожидаемый метод может быть вызван только один раз. Подобно всем
остальным ограничениям числа вызовов, этот выбросит исключение
\Mockery\CountValidator\Exception если произойдет нарушение. Так же может быть
изменено установкой atLeast() и atMost() ограничений.

    twice()

Объявляет, что ожидаемый метод может быть вызван только дважды.

    times(n)

Объявляет, что ожидаемый метод может быть вызван только n раз.

    never()

Объявляет, что ожидаемый метод никогда нельзя вызвать. Никогда!

    atLeast()

Добавляет модификатор минимального числа вызовов ожидаемого метода. Таким
образом atLeast()->times(3) означает что вызов должен произойти минимум три
раза (с данными соответствующими параметрами) и ни в коем случае не меньше чем три раза.

    atMost()

Добавляет модификатор максимального числа вызовов ожидаемого метода. Таким
образом atMost()->times(3) означает, что вызов должен произойти не более чем
три раза. Это также означает что если не было вызовов, то это приемлемо.

    between(min, max)

Устанавливает ожидаемый диапазон числа вызовов. Это фактически идентично
использованию atLeast()->times(min)->atMost()->times(max) но предлагает более
короткую форму. Может сопровождаться вызовом times() без параметра для сохранения
APIs читаемости для естественного языка.

    ordered()

Объявляет, что вызов этого ожидаемого метода должен быть в указанном порядке
относительно так же отмеченных методов. Порядок вызова диктуется порядком в
котором этот модификатор фактически используется, когда устанавливается mock-объект.

    ordered(group)

Объявляет метод, как принадлежащей упорядоченной группе (которая может быть
именованной или нумерованной). Методы в пределах группы можно вызвать в любом
порядке, но упорядоченные вызовы за пределами группы должны быть упорядочены по
отношению к группе, т.е. вы можете задать так, что method1 вызывается перед group1,
которые поочередно вызываются перед вызовом method2.

    globally()

Когда вызывается до ordered() или ordered(group), объявляет что этот порядок
применяется через все mock-объекты (не только для текущего mock-объекта). Это
позволяет диктовать порядок ожиданий через множество mock-объектов.

    byDefault()

Помечает ожидание как по умолчанию. Ожидание по умолчанию применяется, если
другие ожидания не созданы. Более поздние ожидания незамедлительно заменяют
предварительно созданные ожидания объявленные как по умолчанию. Это полезно,
например вы можете установить ожидание по умолчанию в setup() методе вашего
теста и затем настроить их в определенных тестах как надо.

    mock()

Возвращает текущий mock-объект из цепочки ожиданий. Полезно, когда вы
предпочитаете сохранить установку mock-объекта как единое предложение, например:

    $mock = \Mockery::mock('foo')->shouldReceive('foo')->andReturn(1)->mock();

Валидация параметров
-------------------

Параметры переданные в with() объявлении, определяют критерии для соответствия
вызовам ожидаемых методов. Таким образом, вы можете установить много ожиданий для
одного метода, каждый из которых отличается вызовам с ожидаемыми параметрами.
Такое сопоставление параметров делается на "наиболее пригодной" основе. Это
гарантирует что явные соответствия имеют приоритет перед общими соответствиями.


Явное соответствие просто, когда ожидаемый параметр и существующий параметр 
легко приравнять (например используя === или ==). Более обобщенное сопоставление
возможно используя регулярные выражения, подсветку классов и доступные родовые 
сопоставления.  Замысел обобщенный сопоставлений состоит в допустимых параметрах,
определенных в неявных терминах, например Mockery::any() переданный с with() 
будет соответствовать ЛЮБОМУ параметру, переданному в этой позиции.

Вот пример этих возможностей.

    with(1)

Соответствие целому числу 1. Это проходит === тест (идентично). Это действительно
облегчает более строгое == проверку (равенство), когда строка '1' так же соответствовала
параметру.

    with(\Mockery::any())

Соответствует любому параметру. По существу, все что угодно подойдет для этого 
параметра без ограничений.

    with(\Mockery::type('resource'))

Соответствует любому ресурсу, т.е. когда вызов is_resource() вернет true. 
Сопоставление типов, принимает любую строку, которая может быть пристыкована к 
"is_" для формирования валидной проверки типа. Наример, \Mockery::type('float') 
проверяет используя is_float(),а \Mockery::type('callable') использует is_callable(). 
Сопоставление по типам также принимает имя класса или интерфейса, которое будет 
использоваться в instanceof вычислении фактического параметра.

Вы можете найти полый лист доступных проверок на тип по адресу
http://www.php.net/manual/en/ref.var.php

    with(\Mockery::on(closure))

Сопоставление On принимает замыкание (анонимная функция), которому передается 
фактический параметр. Если замыкание вычисляет (т.е. возвращает) булево
TRUE, тогда параметр, как предполагается, соответствует ожиданию. Это неоценимо, 
когда ваш ожидаемый параметр более сложный или просто не реализован в текущем
сопоставлении по умолчанию.

    with('/^foo/')

Объявление параметров также предполагает, что любая строка может быть регулярным
выражением, используемым вместо фактического параметра, когда происходит сопоставление.
Регулярные выражения используются только когда а) нет совпадений === или ==
и б) когда регулярное выражение соответствует валидному выражению (т.е. не 
возвращает false из вызова preg_match()).

    with(\Mockery::ducktype('foo', 'bar'))

Сопоставление Ducktype - это альтернатива сопоставлению по типу класса.
Это простое сравнение любого параметра, который является объектом и содержит 
представленный список методов.

    with(\Mockery::mustBe(2));

MustBe сравнение более строгое чем сопоставление параметра по умолчанию.
Сравнение по умолчанию учитывает преобразования типов PHP, кроме того MustBe также
проверяет что параметр должен быть того же типа, как и ожидаемое значение.
Таким образом, по умолчанию, параметр '2' соответствует фактическому параметру 2
(целое число), но MustBe привел бы к сбою в той же самой ситуации, т.к. ожидаемый
параметр должен быть строкой, вместо этого мы получили целое число.

Замечание: Объекты не подвергаются идентичному сравнению используя это сопоставление,
т.к. PHP приведет к сбою, если сравниваемые объекты не одного и того же типа.
Это помеха, когда объекты генерируются перед возвратом, так идентифицирующее 
сравнение никогда не было бы возможным.

    with(\Mockery::not(2))

Сопоставление Not сравнивает любой аргумент, который не равен или идентичен
указанному параметру.

    with(\Mockery::anyOf(1, 2))

Сопоставляет любой параметр который равен любому одному из данных параметров.

    with(\Mockery::notAnyof(1, 2))

Сопоставляет любой параметр, который не равен или идентичен любому из данных 
параметров.

    with(\Mockery::subset(array(0=>'foo')))

Сопоставляет любой параметр, который является любым массивом содержащим указанный
подмассив. Это гарантирует что и ключи и значения каждого фактического элемента 
будут сравниваться.

    with(\Mockery::contains(value1, value2))

Сопоставляет любой параметр, который является массивом и содержит перечисленные 
значения. Имена ключей игнорируются.

    with(\Mockery::hasKey(key));

Сопоставляет любой параметр, который является массивом и содержит указанный ключ.

    with(\Mockery::hasValue(value));

Спопставляет любой параметр, который является массивом и содержит указанное значение.

Создание Частичных Mock-объектов
---------------------------------

Частичная подмена используется когда вам необходимо подменить только несколько 
методов объекта, оставляя остаток свободным для обычных вызовов (т.е. как реализованно).

В отличии от других mock-объектов, Mockery для частичной подмены, использует 
реальный конкретный объект. Этот подход к частичным подменам предназначен для обхода
многих неприятных проблем. Например, части могут требовать установку параметров
через конструктор или других установок/внедрений прежде чем использоваться.
Попытка выполнить это автоматически через Mockery не является так же интуитивно,
как выполнение это нормальным способом - а затем передача объекта в Mockery.

Частичные mock-объекты поэтому и созданы как Прокси с внедренными реальными объектами.
Прокси сами по себе наследуют тип от внедряемых объектов (безопасные типы) и тем 
самым ведут себя, подобно любому другому Mockery-базед подмененному объекту, позволяя
вам динамически определять ожидания. Эта гибкость означает, что есть небольшое
upfront определение (помимо установки реального объекта) и вы можете устанавливать
ожидания по умолчанию, а так же упорядочивать на лету.

Ожидания По-умолчанию
-------------------------

Often in unit testing, we end up with sets of tests which use the same object
dependency over and over again. Rather than mocking this class/object within
every single unit test (requiring a mountain of duplicate code), we can instead
define reusable default mocks within the test case's setup() method. This even
works where unit tests use varying expectations on the same or similar mock
object.

How this works, is that you can define mocks with default expectations. Then,
in a later unit test, you can add or fine-tune expectations for that
specific test. Any expectation can be set as a default using the byDefault()
declaration.

Mocking Demeter Chains And Fluent Interfaces
--------------------------------------------

Both of these terms refer to the growing practice of invoking statements
similar to:

    $object->foo()->bar()->zebra()->alpha()->selfDestruct();

The long chain of method calls isn't necessarily a bad thing, assuming they
each link back to a local object the calling class knows. Just as a fun example,
Mockery's long chains (after the first shouldReceive() method) all call to the
same instance of \Mockery\Expectation. However, sometimes this is not the case
and the chain is constantly crossing object boundaries.

In either case, mocking such a chain can be a horrible task. To make it easier
Mockery support demeter chain mocking. Essentially, we shortcut through the
chain and return a defined value from the final call. For example, let's
assume selfDestruct() returns the string "Ten!" to $object (an instance of
CaptainsConsole). Here's how we could mock it.

    $mock = \Mockery::mock('CaptainsConsole');
    $mock->shouldReceive('foo->bar->zebra->alpha->selfDestruct')->andReturn('Ten!');

The above expectation can follow any previously seen format or expectation, except
that the method name is simply the string of all expected chain calls separated
by "->". Mockery will automatically setup the chain of expected calls with
its final return values, regardless of whatever intermediary object might be
used in the real implementation.

Arguments to all members of the chain (except the final call) are ignored in
this process.

Mock Object Recording
---------------------

In certain cases, you may find that you are testing against an already
established pattern of behaviour, perhaps during refactoring. Rather then hand
crafting mock object expectations for this behaviour, you could instead use
the existing source code to record the interactions a real object undergoes
onto a mock object as expectations - expectations you can then verify against
an alternative or refactored version of the source code.

To record expectations, you need a concrete instance of the class to be mocked.
This can then be used to create a partial mock to which is given the necessary
code to execute the object interactions to be recorded. A simple example is
outline below (we use a closure for passing instructions to the mock).

Here we have a very simple setup, a class (SubjectUser) which uses another class
(Subject) to retrieve some value. We want to record as expectations on our
mock (which will replace Subject later) all the calls and return values of
a Subject instance when interacting with SubjectUser.

    class Subject {

        public function execute() {
            return 'executed!';
        }
    }

    class SubjectUser {

        public function use(Subject $subject) {
            return $subject->execute();
        }
    }

Here's the test case showing the recording:

    class SubjectUserTest extends extends PHPUnit_Framework_TestCase
    {

        public function teardown()
        {
            \Mockery::close();
        }

        public function testSomething()
        {
            $mock = \Mockery::mock(new Subject);
            $mock->shouldExpect(function ($subject) {
                $user = new SubjectUser;
                $user->use($subject);
            });

            /**
             * Assume we have a replacement SubjectUser called NewSubjectUser.
             * We want to verify it behaves identically to SubjectUser, i.e.
             * it uses Subject in the exact same way
             */
            $newSubject = new NewSubjectUser;
            $newSubject->use($mock);
        }

    }

After the \Mockery::close() call in teardown() validates the mock object, we
should have zero exceptions if NewSubjectUser acted on Subject in a similar way
to SubjectUser. By default the order of calls are not enforced, and loose argument
matching is enabled, i.e. arguments may be equal (==) but not necessarily identical
(===).

If you wished to be more strict, for example ensuring the order of calls
and the final call counts were identical, or ensuring arguments are completely
identical, you can invoke the recorder's strict mode from the closure block, e.g.

    $mock->shouldExpect(function ($subject) {
        $subject->shouldBeStrict();
        $user = new SubjectUser;
        $user->use($subject);
    });

Dealing with Final Classes/Methods
----------------------------------

One of the primary restrictions of mock objects in PHP, is that mocking classes
or methods marked final is hard. The final keyword prevents methods so marked
from being replaced in subclasses (subclassing is how mock objects can inherit
the type of the class or object being mocked.

The simplest solution is not to mark classes or methods as final!

However, in a compromise between mocking functionality and type safety, Mockery
does allow creating partial mocks from classes marked final, or from classes with
methods marked final. This offers all the usual mock object goodness but the
resulting mock will not inherit the class type of the object being mocked, i.e.
it will not pass any instanceof comparison.

Mockery Global Configuration
----------------------------

To allow for a degree of fine-tuning, Mockery utilises a singleton configuration
object to store a small subset of core behaviours. The two currently present
include:

* Allowing the mocking of methods which do not actually exist
* Allowing the existence of expectations which are never fulfilled (i.e. unused)

By default, these behaviours are enabled. Of course, there are situations where
this can lead to unintended consequences. The mocking of non-existent methods
may allow mocks based on real classes/objects to fall out of sync with the
actual implementations, especially when some degree of integration testing (testing
of object wiring) is not being performed. Allowing unfulfilled expectations means
unnecessary mock expectations go unnoticed, cluttering up test code, and
potentially confusing test readers.

You may allow or disallow these behaviours (whether for whole test suites or just
select tests) by using one or both of the following two calls:

    \Mockery::getConfiguration()->allowMockingNonExistentMethods(bool);
    \Mockery::getConfiguration()->allowMockingMethodsUnnecessarily(bool);

Passing a true allows the behaviour, false disallows it. Both take effect
immediately until switched back. In both cases, if either
behaviour is detected when not allowed, it will result in an Exception being
thrown at that point. Note that disallowing these behaviours should be carefully
considered since they necessarily remove at least some of Mockery's flexibility.

Reserved Method Names
---------------------

As you may have noticed, Mockery uses a number of methods called directly on
all mock objects, for example shouldReceive(). Such methods are necessary
in order to setup expectations on the given mock, and so they cannot be
implemented on the classes or objects being mocked without creating a method
name collision (reported as a PHP fatal error). The methods reserved by Mockery are:

* shouldReceive()
* shouldBeStrict()

In addition, all mocks utilise a set of added methods and protected properties
which cannot exist on the class or object being mocked. These are far less likely
to cause collisions. All properties are prefixed with "_mockery" and all method
names with "mockery_".

Quick Examples
--------------

Create a mock object to return a sequence of values from a set of method calls.

    class SimpleTest extends extends PHPUnit_Framework_TestCase
    {

        public function teardown()
        {
            \Mockery::close();
        }

        public function testSimpleMock()
        {
            $mock = \Mockery::mock(array('pi' => 3.1416, 'e' => 2.71));
            $this->assertEquals(3.1416, $mock->pi());
            $this->assertEquals(2.71, $mock->e());
        }

    }

Create a mock object which returns a self-chaining Undefined object for a method
call.

    use \Mockery as m;

    class UndefinedTest extends extends PHPUnit_Framework_TestCase
    {

        public function teardown()
        {
            m::close();
        }

        public function testUndefinedValues()
        {
            $mock = m::mock('my mock');
            $mock->shouldReceive('divideBy')->with(0)->andReturnUndefined();
            $this->assertTrue($mock->divideBy(0) instanceof \Mockery\Undefined);
        }

    }

Creates a mock object which multiple query calls and a single update call

    use \Mockery as m;

    class DbTest extends extends PHPUnit_Framework_TestCase
    {

        public function teardown()
        {
            m::close();
        }

        public function testDbAdapter()
        {
            $mock = m::mock('db');
            $mock->shouldReceive('query')->andReturn(1, 2, 3);
            $mock->shouldReceive('update')->with(5)->andReturn(NULL)->once();

            // test code here using the mock
        }

    }

Expect all queries to be executed before any updates.

    use \Mockery as m;

    class DbTest extends extends PHPUnit_Framework_TestCase
    {

        public function teardown()
        {
            m::close();
        }

        public function testQueryAndUpdateOrder()
        {
            $mock = m::mock('db');
            $mock->shouldReceive('query')->andReturn(1, 2, 3)->ordered();
            $mock->shouldReceive('update')->andReturn(NULL)->once()->ordered();

            // test code here using the mock
        }

    }

Create a mock object where all queries occur after startup, but before finish, and
where queries are expected with several different params.

    use \Mockery as m;

    class DbTest extends extends PHPUnit_Framework_TestCase
    {

        public function teardown()
        {
            m::close();
        }

        public function testOrderedQueries()
        {
            $db = m::mock('db');
            $db->shouldReceive('startup')->once()->ordered();
            $db->shouldReceive('query')->with('CPWR')->andReturn(12.3)->once()->ordered('queries');
            $db->shouldReceive('query')->with('MSFT')->andReturn(10.0)->once()->ordered('queries');
            $db->shouldReceive('query')->with("/^....$/")->andReturn(3.3)->atLeast()->once()->ordered('queries');
            $db->shouldReceive('finish')->once()->ordered();

            // test code here using the mock
        }

    }
