<?php


namespace Alura\Leilao\Tests\Integration\Dao;


use Alura\Leilao\Dao\Leilao as LeilaoDao;
use Alura\Leilao\Infra\ConnectionCreator;
use Alura\Leilao\Model\Leilao;
use PHPUnit\Framework\TestCase;

class LeilaoDaoTest extends TestCase
{

    /** @var \PDO */
    private static $_pdo;

    public static function setUpBeforeClass(): void
    {
        // cria banco em memória, para agilizar os testes
        self::$_pdo = new \PDO('sqlite::memory:');

        // cria estrutura do banco
        self::$_pdo->exec('
            create table leiloes
            (
                id INTEGER primary key,
                descricao TEXT,
                finalizado BOOL,
                dataInicio TEXT
            );
        ');

    }

    protected function setUp(): void
    {
        self::$_pdo->beginTransaction();
    }

    public function testInsercaoEBuscaDevemFuncionar() {
        // arrange
        $leilao     = new Leilao('Apple Pencil 2020');
        $leilaoDao  = new LeilaoDao(self::$_pdo);

        // act
        $leilaoDao->salva($leilao);
        $leiloes = $leilaoDao->recuperarNaoFinalizados();

        // assert
        self::assertCount(1, $leiloes);
        self::assertContainsOnlyInstancesOf(Leilao::class, $leiloes);

        // assertSame: verifica conteúdo e tipo (===)
        self::assertSame('Apple Pencil 2020', $leiloes[0]->recuperarDescricao());

    }

    protected function tearDown(): void
    {
        self::$_pdo->rollBack();
    }

}