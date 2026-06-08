<?php
$json_file = 'data.json';

// Tratar o formulário quando salvo
$mensagem = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Montar o array de serviços
    $servicos = [];
    if(isset($_POST['servico_titulo']) && is_array($_POST['servico_titulo'])) {
        for($i=0; $i < count($_POST['servico_titulo']); $i++) {
            $servicos[] = [
                "titulo" => $_POST['servico_titulo'][$i] ?? '',
                "descricao" => $_POST['servico_descricao'][$i] ?? '',
                "imagem" => $_POST['servico_imagem'][$i] ?? '',
                "tipo_link" => $_POST['servico_link'][$i] ?? 'nenhum'
            ];
        }
    }

    $novosDados = [
        "nome" => $_POST['nome'] ?? '',
        "crp" => $_POST['crp'] ?? '',
        "slogan" => $_POST['slogan'] ?? '',
        "sobre_mim" => $_POST['sobre_mim'] ?? '',
        "whatsapp_numero" => $_POST['whatsapp_numero'] ?? '',
        "mensagem_padrao" => $_POST['mensagem_padrao'] ?? '',
        "mensagem_agendamento" => $_POST['mensagem_agendamento'] ?? '',
        "mensagem_palestra" => $_POST['mensagem_palestra'] ?? '',
        "imagem_hero_bg" => $_POST['imagem_hero_bg'] ?? '',
        "imagem_perfil" => $_POST['imagem_perfil'] ?? '',
        "link_instagram" => $_POST['link_instagram'] ?? '',
        "link_mapa" => $_POST['link_mapa'] ?? '',
        "servicos" => $servicos
    ];
    
    // Salvar no JSON
    if(file_put_contents($json_file, json_encode($novosDados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))) {
        $mensagem = '<div class="alert alert-success">Configurações salvas com sucesso! O site já foi atualizado.</div>';
    } else {
        $mensagem = '<div class="alert alert-danger">Erro ao salvar as configurações. Verifique as permissões de escrita na pasta.</div>';
    }
}

// Ler os dados atuais para popular o formulário
$dados = [];
if (file_exists($json_file)) {
    $conteudo = file_get_contents($json_file);
    $dados = json_decode($conteudo, true) ?: [];
}

function getValue($key, $data) {
    return isset($data[$key]) ? htmlspecialchars($data[$key]) : '';
}
$servicos = isset($dados['servicos']) && is_array($dados['servicos']) ? $dados['servicos'] : [];
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Painel Administrativo - Victtoria Martins</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; padding-bottom: 80px; }
        .card { box-shadow: 0 4px 6px rgba(0,0,0,0.1); border: none; margin-bottom: 30px; }
        .card-header { background-color: #59110C; color: #fff; font-weight: bold; }
        .service-box { border: 1px solid #dee2e6; border-radius: 8px; padding: 15px; margin-bottom: 15px; background-color: #fff; }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark mb-4" style="background-color: #3E2723;">
        <div class="container">
            <span class="navbar-brand mb-0 h1">⚙️ Painel de Configurações</span>
            <a href="../index.html" class="btn btn-outline-light btn-sm" target="_blank">Ver Site</a>
        </div>
    </nav>

    <div class="container">
        <?= $mensagem ?>
        <form method="POST" action="">
            <ul class="nav nav-tabs mb-4" id="myTab" role="tablist">
              <li class="nav-item" role="presentation">
                <button class="nav-link active" id="geral-tab" data-bs-toggle="tab" data-bs-target="#geral" type="button" role="tab">Geral & Textos</button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="servicos-tab" data-bs-toggle="tab" data-bs-target="#servicos" type="button" role="tab">Áreas de Atuação</button>
              </li>
            </ul>

            <div class="tab-content" id="myTabContent">
                
                <!-- ABA: GERAL -->
                <div class="tab-pane fade show active" id="geral" role="tabpanel">
                    <div class="row">
                        <!-- Coluna 1 -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">Textos Principais</div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Nome</label>
                                        <input type="text" class="form-control" name="nome" value="<?= getValue('nome', $dados) ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">CRP</label>
                                        <input type="text" class="form-control" name="crp" value="<?= getValue('crp', $dados) ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Slogan (Página Inicial)</label>
                                        <input type="text" class="form-control" name="slogan" value="<?= getValue('slogan', $dados) ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Texto: Sobre Mim</label>
                                        <textarea class="form-control" name="sobre_mim" rows="5"><?= getValue('sobre_mim', $dados) ?></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header">Links e Redes Sociais</div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Link do Instagram</label>
                                        <input type="text" class="form-control" name="link_instagram" value="<?= getValue('link_instagram', $dados) ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Link do Google Maps (Localização)</label>
                                        <input type="text" class="form-control" name="link_mapa" value="<?= getValue('link_mapa', $dados) ?>">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Coluna 2 -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">WhatsApp & Mensagens</div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Número do WhatsApp (apenas números)</label>
                                        <input type="text" class="form-control" name="whatsapp_numero" value="<?= getValue('whatsapp_numero', $dados) ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Mensagem Padrão (Botão Flutuante)</label>
                                        <input type="text" class="form-control" name="mensagem_padrao" value="<?= getValue('mensagem_padrao', $dados) ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Mensagem para Agendamento</label>
                                        <input type="text" class="form-control" name="mensagem_agendamento" value="<?= getValue('mensagem_agendamento', $dados) ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Mensagem para Palestras</label>
                                        <input type="text" class="form-control" name="mensagem_palestra" value="<?= getValue('mensagem_palestra', $dados) ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card">
                                <div class="card-header">Imagens Globais (Cole os Links)</div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Fundo da Página Inicial (Hero Background)</label>
                                        <input type="text" class="form-control" name="imagem_hero_bg" value="<?= getValue('imagem_hero_bg', $dados) ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Foto de Perfil</label>
                                        <input type="text" class="form-control" name="imagem_perfil" value="<?= getValue('imagem_perfil', $dados) ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ABA: ÁREAS DE ATUAÇÃO -->
                <div class="tab-pane fade" id="servicos" role="tabpanel">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span>Gerenciar Áreas de Atuação</span>
                            <button type="button" class="btn btn-sm btn-light" onclick="addService()">+ Adicionar Área</button>
                        </div>
                        <div class="card-body" id="services-container">
                            <?php foreach($servicos as $index => $servico): ?>
                            <div class="service-box">
                                <div class="d-flex justify-content-between">
                                    <h5>Área de Atuação</h5>
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.parentElement.parentElement.remove()">Remover</button>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label">Título</label>
                                        <input type="text" class="form-control" name="servico_titulo[]" value="<?= htmlspecialchars($servico['titulo'] ?? '') ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label">Link da Imagem</label>
                                        <input type="text" class="form-control" name="servico_imagem[]" value="<?= htmlspecialchars($servico['imagem'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-8 mb-2">
                                        <label class="form-label">Descrição Breve</label>
                                        <input type="text" class="form-control" name="servico_descricao[]" value="<?= htmlspecialchars($servico['descricao'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <label class="form-label">Link do Botão (Opcional)</label>
                                        <select class="form-control" name="servico_link[]">
                                            <option value="nenhum" <?= ($servico['tipo_link'] ?? '') === 'nenhum' ? 'selected' : '' ?>>Sem Link</option>
                                            <option value="agendamento" <?= ($servico['tipo_link'] ?? '') === 'agendamento' ? 'selected' : '' ?>>WhatsApp (Agendamento)</option>
                                            <option value="palestra" <?= ($servico['tipo_link'] ?? '') === 'palestra' ? 'selected' : '' ?>>WhatsApp (Palestra)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

            </div>
            
            <div class="fixed-bottom bg-white p-3 border-top text-end shadow-sm" style="z-index: 1000;">
                <div class="container">
                    <button type="submit" class="btn btn-success btn-lg px-5">💾 Salvar Alterações</button>
                </div>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function addService() {
            const container = document.getElementById('services-container');
            const html = `
            <div class="service-box">
                <div class="d-flex justify-content-between">
                    <h5>Área de Atuação Nova</h5>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.parentElement.parentElement.remove()">Remover</button>
                </div>
                <div class="row mt-2">
                    <div class="col-md-6 mb-2">
                        <label class="form-label">Título</label>
                        <input type="text" class="form-control" name="servico_titulo[]" required>
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="form-label">Link da Imagem</label>
                        <input type="text" class="form-control" name="servico_imagem[]">
                    </div>
                    <div class="col-md-8 mb-2">
                        <label class="form-label">Descrição Breve</label>
                        <input type="text" class="form-control" name="servico_descricao[]">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Link do Botão (Opcional)</label>
                        <select class="form-control" name="servico_link[]">
                            <option value="nenhum">Sem Link</option>
                            <option value="agendamento">WhatsApp (Agendamento)</option>
                            <option value="palestra">WhatsApp (Palestra)</option>
                        </select>
                    </div>
                </div>
            </div>`;
            container.insertAdjacentHTML('beforeend', html);
        }
    </script>
</body>
</html>
