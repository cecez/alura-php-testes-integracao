<?php


namespace Alura\Leilao\Tests\Integration\Dao;


use Alura\Leilao\Dao\Leilao as LeilaoDao;
use Alura\Leilao\Model\Leilao;
use PDO;
use PHPUnit\Framework\TestCase;

class LeilaoDaoTest extends TestCase
{

    /** @var PDO */
    private static $_pdo;

    public static function setUpBeforeClass(): void
    {
        // cria banco em memória, para agilizar os testes
        self::$_pdo = new PDO('sqlite::memory:');

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

    /**
     * @dataProvider leiloes
     * @param  array  $leiloes
     */
    public function testBuscaLeiloesNaoFinalizados(array $leiloes) {

        // arrange
        $leilaoDao  = new LeilaoDao(self::$_pdo);
        foreach ($leiloes as $leilao) {
            $leilaoDao->salva($leilao);
        }

        // act
        $leiloes = $leilaoDao->recuperarNaoFinalizados();

        // assert
        self::assertCount(1, $leiloes);
        self::assertContainsOnlyInstancesOf(Leilao::class, $leiloes);

        // assertSame: verifica conteúdo e tipo (===)
        self::assertSame('Apple Pencil 2020', $leiloes[0]->recuperarDescricao());

    }

    /**
     * @dataProvider leiloes
     * @param  array  $leiloes
     */
    public function testBuscaLeiloesFinalizados(array $leiloes) {
        // arrange
        $leilaoDao  = new LeilaoDao(self::$_pdo);
        foreach ($leiloes as $leilao) {
            $leilaoDao->salva($leilao);
        }

        // act
        $leiloes = $leilaoDao->recuperarFinalizados();

        // assert
        self::assertCount(1, $leiloes);
        self::assertContainsOnlyInstancesOf(Leilao::class, $leiloes);

        // assertSame: verifica conteúdo e tipo (===)
        self::assertSame('Caneta 2020', $leiloes[0]->recuperarDescricao());

    }

    public function testAoAtualizarLeilaoStatusDeveSerAlterado()
    {
        // arrange
        $leilao = new Leilao('Xícara 2020');
        $leilaoDao = new LeilaoDao(self::$_pdo);
        $leilao = $leilaoDao->salva($leilao);
        $leilao->finaliza();

        // act
        $leilaoDao->atualiza($leilao);

        // assert
        $leiloes = $leilaoDao->recuperarFinalizados();
        self::assertCount(1, $leiloes);
        self::assertSame('Xícara 2020', $leiloes[0]->recuperarDescricao());
    }

    public function leiloes()
    {
        $naoFinalizado  = new Leilao('Apple Pencil 2020');  // leilão não finalizado
        $finalizado     = new Leilao('Caneta 2020');
        $finalizado->finaliza();                                    // leilão finalizado

        return [
            [
                [$naoFinalizado, $finalizado]
            ]
        ];

    }

    protected function tearDown(): void
    {
        self::$_pdo->rollBack();
    }

}