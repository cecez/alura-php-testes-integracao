// Testes para rodar no Postman

pm.test("Código do status da resposta deve ser 200", () => {
    pm.response.to.have.status(200);
});

pm.test("Resultado deve ser em JSON", () => {
    pm.response.to.be.json;
});

const leiloes = pm.response.json();

pm.test('O esquema da resposta é válido', () => {

    const schema = {
        "required" : ['descricao', 'estaFinalizado'],
        "properties": {
            "descricao": { "type": "string" },
            "estaFinalizado": { "type": "boolean" }
        }
    };

    leiloes.forEach(leilao => pm.expect(tv4.validate(leilao, schema)).to.be.true)

});

pm.test("Nenhum leilão deve estar finalizado", () => {
    leiloes.forEach(leilao => pm.expect(leilao.estaFinalizado).to.be.false)
});