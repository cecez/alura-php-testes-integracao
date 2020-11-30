<?php


namespace Alura\Leilao\Tests\Integration\Dao;


use Alura\Leilao\Dao\Leilao as LeilaoDao;
use Alura\Leilao\Infra\ConnectionCreator;
use Alura\Leilao\Model\Leilao;
use PHPUnit\Framework\TestCase;

class LeilaoDaoTest extends TestCase
{

    /** @var \PDO */
    private $_pdo;

    protected function setUp(): void
    {
        $this->_pdo = ConnectionCreator::getConnection();
        $this->_pdo->beginTransaction();
    }

    public function testInsercaoEBuscaDevemFuncionar() {
        // arrange
        $leilao     = new Leilao('Apple Pencil 2020');
        $leilaoDao  = new LeilaoDao($this->_pdo);

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
        $this->_pdo->rollBack();
    }

}