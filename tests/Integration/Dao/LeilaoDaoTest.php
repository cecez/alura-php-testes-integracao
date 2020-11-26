<?php


namespace Alura\Leilao\Tests\Integration\Dao;


use Alura\Leilao\Dao\Leilao as LeilaoDao;
use Alura\Leilao\Infra\ConnectionCreator;
use Alura\Leilao\Model\Leilao;
use PHPUnit\Framework\TestCase;

class LeilaoDaoTest extends TestCase
{

    public function testInsercaoEBuscaDevemFuncionar() {
        // arrange
        $leilao     = new Leilao('Apple Pencil 2020');
        $pdo        = ConnectionCreator::getConnection();
        $leilaoDao  = new LeilaoDao($pdo);

        // act
        $leilaoDao->salva($leilao);
        $leiloes = $leilaoDao->recuperarNaoFinalizados();

        // assert
        self::assertCount(1, $leiloes);
        self::assertContainsOnlyInstancesOf(Leilao::class, $leiloes);

        // assertSame: verifica conteúdo e tipo (===)
        self::assertSame('Apple Pencil 2020', $leiloes[0]->recuperarDescricao());

        // tear down
        $pdo->exec('DELETE FROM leiloes');
    }

}