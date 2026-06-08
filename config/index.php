<?php
$json_file = 'data.json';

// Função auxiliar para converter base64 em arquivo
function processImageBase64($base64string, $prefix) {
    if (strpos($base64string, 'data:image/') === 0) {
        $parts = explode(',', $base64string);
        $data = base64_decode($parts[1]);
        $filename = $prefix . '_' . uniqid() . '.jpg';
        $filepath = '../img/' . $filename;
        file_put_contents($filepath, $data);
        return 'img/' . $filename;
    }
    return $base64string; // Retorna original se não for base64
}

$mensagem = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Processar imagens globais
    $imgHero = processImageBase64($_POST['imagem_hero_bg'] ?? '', 'hero');
    $imgPerfil = processImageBase64($_POST['imagem_perfil'] ?? '', 'perfil');

    // Montar o array de serviços processando imagens individualmente
    $servicos = [];
    if(isset($_POST['servico_titulo']) && is_array($_POST['servico_titulo'])) {
        for($i=0; $i < count($_POST['servico_titulo']); $i++) {
            $imgService = processImageBase64($_POST['servico_imagem'][$i] ?? '', 'servico');
            $servicos[] = [
                "titulo" => $_POST['servico_titulo'][$i] ?? '',
                "descricao" => $_POST['servico_descricao'][$i] ?? '',
                "imagem" => $imgService,
                "link_texto" => $_POST['servico_link_texto'][$i] ?? '',
                "whatsapp_msg" => $_POST['servico_whatsapp_msg'][$i] ?? ''
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
        "imagem_hero_bg" => $imgHero,
        "imagem_perfil" => $imgPerfil,
        "link_instagram" => $_POST['link_instagram'] ?? '',
        "link_mapa" => $_POST['link_mapa'] ?? '',
        "servicos" => $servicos
    ];
    
    if(file_put_contents($json_file, json_encode($novosDados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))) {
        $mensagem = '<div class="alert alert-success">Configurações e imagens salvas com sucesso!</div>';
    } else {
        $mensagem = '<div class="alert alert-danger">Erro ao salvar as configurações.</div>';
    }
}

$dados = [];
if (file_exists($json_file)) {
    $conteudo = file_get_contents($json_file);
    $dados = json_decode($conteudo, true) ?: [];
}

function getValue($key, $data) {
    return isset($data[$key]) ? htmlspecialchars($data[$key]) : '';
}

function getImgSrc($path) {
    if(empty($path)) return '';
    if(strpos($path, 'http') === 0 || strpos($path, 'data:image') === 0) return htmlspecialchars($path);
    return '../' . htmlspecialchars($path);
}

$servicos = isset($dados['servicos']) && is_array($dados['servicos']) ? $dados['servicos'] : [];
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Painel Administrativo - Victtoria Martins</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Cropper CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; padding-bottom: 80px; }
        .card { box-shadow: 0 4px 6px rgba(0,0,0,0.1); border: none; margin-bottom: 30px; }
        .card-header { background-color: #59110C; color: #fff; font-weight: bold; }
        .service-box { border: 1px solid #dee2e6; border-radius: 8px; padding: 15px; margin-bottom: 15px; background-color: #fff; border-left: 5px solid #B17F31; }
        
        /* Cropper Modal Styles */
        #img-crop-container { width: 100%; max-height: 60vh; text-align: center; }
        #img-crop-container img { max-width: 100%; }
        .img-preview-box { border: 2px dashed #ccc; padding: 10px; border-radius: 8px; text-align: center; background: #fff; margin-bottom: 10px; }
        .img-preview-box img { max-width: 100%; max-height: 200px; border-radius: 4px; }
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
                                <div class="card-header">WhatsApp & Contato Geral</div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Número do WhatsApp (apenas números)</label>
                                        <input type="text" class="form-control" name="whatsapp_numero" value="<?= getValue('whatsapp_numero', $dados) ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Mensagem Padrão (Botão Flutuante e Banner Hero)</label>
                                        <input type="text" class="form-control" name="mensagem_padrao" value="<?= getValue('mensagem_padrao', $dados) ?>">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">Imagens Principais (Upload Local)</div>
                                <div class="card-body">
                                    
                                    <!-- Imagem Hero -->
                                    <div class="mb-4">
                                        <label class="form-label fw-bold">Fundo da Página Inicial (Hero Background)</label>
                                        <div class="img-preview-box">
                                            <?php $heroPath = getImgSrc(getValue('imagem_hero_bg', $dados)); ?>
                                            <img id="preview_hero" src="<?= $heroPath ?>" style="display: <?= $heroPath ? 'inline-block' : 'none' ?>;">
                                        </div>
                                        <input type="hidden" name="imagem_hero_bg" id="input_hero" value="<?= getValue('imagem_hero_bg', $dados) ?>">
                                        <button type="button" class="btn btn-primary w-100" onclick="triggerCrop('input_hero', 'preview_hero', 16/9)"><i class="fa-solid fa-camera"></i> Escolher e Recortar Imagem</button>
                                    </div>

                                    <!-- Imagem Perfil -->
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Foto de Perfil</label>
                                        <div class="img-preview-box">
                                            <?php $perfilPath = getImgSrc(getValue('imagem_perfil', $dados)); ?>
                                            <img id="preview_perfil" src="<?= $perfilPath ?>" style="display: <?= $perfilPath ? 'inline-block' : 'none' ?>;">
                                        </div>
                                        <input type="hidden" name="imagem_perfil" id="input_perfil" value="<?= getValue('imagem_perfil', $dados) ?>">
                                        <button type="button" class="btn btn-primary w-100" onclick="triggerCrop('input_perfil', 'preview_perfil', 1)"><i class="fa-solid fa-camera"></i> Escolher e Recortar (Quadrada)</button>
                                    </div>

                                </div>
                            </div>
                            
                            <div class="card">
                                <div class="card-header">Links Sociais</div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Link do Instagram</label>
                                        <input type="text" class="form-control" name="link_instagram" value="<?= getValue('link_instagram', $dados) ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Link do Google Maps</label>
                                        <input type="text" class="form-control" name="link_mapa" value="<?= getValue('link_mapa', $dados) ?>">
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
                            <button type="button" class="btn btn-sm btn-light text-dark fw-bold" onclick="addService()">+ Adicionar Área</button>
                        </div>
                        <div class="card-body" id="services-container">
                            <?php foreach($servicos as $index => $servico): ?>
                            <div class="service-box">
                                <div class="d-flex justify-content-between mb-3">
                                    <h5 class="text-secondary"><i class="fa-solid fa-layer-group"></i> Área de Atuação</h5>
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.parentElement.parentElement.remove()">Excluir</button>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Título</label>
                                        <input type="text" class="form-control" name="servico_titulo[]" value="<?= htmlspecialchars($servico['titulo'] ?? '') ?>" required>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label fw-bold">Descrição Breve</label>
                                        <input type="text" class="form-control" name="servico_descricao[]" value="<?= htmlspecialchars($servico['descricao'] ?? '') ?>">
                                    </div>
                                    
                                    <!-- Imagem do Serviço com Cropper -->
                                    <div class="col-md-12 mb-4">
                                        <label class="form-label fw-bold">Imagem do Serviço</label>
                                        <div class="row align-items-center">
                                            <div class="col-md-4">
                                                <div class="img-preview-box m-0">
                                                    <?php $svcPath = getImgSrc($servico['imagem'] ?? ''); ?>
                                                    <img id="preview_svc_<?= $index ?>" src="<?= $svcPath ?>" style="display: <?= $svcPath ? 'inline-block' : 'none' ?>;">
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="hidden" name="servico_imagem[]" id="input_svc_<?= $index ?>" value="<?= htmlspecialchars($servico['imagem'] ?? '') ?>">
                                                <button type="button" class="btn btn-outline-primary" onclick="triggerCrop('input_svc_<?= $index ?>', 'preview_svc_<?= $index ?>', 16/9)"><i class="fa-solid fa-upload"></i> Escolher Foto do Computador</button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-4 mb-2">
                                        <label class="form-label fw-bold text-success">Texto do Botão</label>
                                        <input type="text" class="form-control border-success" name="servico_link_texto[]" value="<?= htmlspecialchars($servico['link_texto'] ?? '') ?>" placeholder="Ex: Agendar (Vazio oculta o botão)">
                                    </div>
                                    <div class="col-md-8 mb-2">
                                        <label class="form-label fw-bold text-success">Mensagem Automática (WhatsApp)</label>
                                        <input type="text" class="form-control border-success" name="servico_whatsapp_msg[]" value="<?= htmlspecialchars($servico['whatsapp_msg'] ?? '') ?>" placeholder="Ex: Olá, gostaria de saber mais sobre...">
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
                    <button type="submit" class="btn btn-success btn-lg px-5">💾 Salvar Alterações e Recortes</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Hidden Input File (Triggered via JS) -->
    <input type="file" id="global-file-input" accept="image/png, image/jpeg, image/jpg" style="display: none;">

    <!-- Modal do Cropper -->
    <div class="modal fade" id="cropperModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Recortar Imagem</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body bg-light">
            <div id="img-crop-container">
                <img id="image-to-crop" src="" alt="Picture">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-success" id="btn-crop">✂️ Confirmar Recorte</button>
          </div>
        </div>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
    
    <script>
        let cropper;
        let currentTargetInputId = '';
        let currentTargetPreviewId = '';
        let currentAspectRatio = 1;

        const fileInput = document.getElementById('global-file-input');
        const imageToCrop = document.getElementById('image-to-crop');
        const modalElement = document.getElementById('cropperModal');
        const cropperModal = new bootstrap.Modal(modalElement);

        // Gera ID aleatorio para novos servicos
        function genId() { return Math.random().toString(36).substr(2, 9); }

        function triggerCrop(inputId, previewId, aspectRatio) {
            currentTargetInputId = inputId;
            currentTargetPreviewId = previewId;
            currentAspectRatio = aspectRatio;
            fileInput.click();
        }

        // Quando o usuario seleciona um arquivo
        fileInput.addEventListener('change', function (e) {
            const files = e.target.files;
            if (files && files.length > 0) {
                const reader = new FileReader();
                reader.onload = function (event) {
                    imageToCrop.src = event.target.result;
                    cropperModal.show();
                    // Limpar input para poder selecionar o mesmo arquivo novamente se precisar
                    fileInput.value = '';
                };
                reader.readAsDataURL(files[0]);
            }
        });

        // Inicializar Cropper quando modal abrir
        modalElement.addEventListener('shown.bs.modal', function () {
            cropper = new Cropper(imageToCrop, {
                aspectRatio: currentAspectRatio,
                viewMode: 1,
                autoCropArea: 1,
                dragMode: 'move',
            });
        });

        // Destruir Cropper quando modal fechar
        modalElement.addEventListener('hidden.bs.modal', function () {
            if (cropper) {
                cropper.destroy();
                cropper = null;
            }
        });

        // Botão de Confirmar Recorte
        document.getElementById('btn-crop').addEventListener('click', function () {
            if (!cropper) return;
            const canvas = cropper.getCroppedCanvas({
                width: 1200, // max width resolution to save
            });
            
            // Gerar base64 JPG (qualidade 0.8)
            const base64Img = canvas.toDataURL('image/jpeg', 0.8);
            
            // Injetar no hidden input e no preview
            document.getElementById(currentTargetInputId).value = base64Img;
            const previewElem = document.getElementById(currentTargetPreviewId);
            previewElem.src = base64Img;
            previewElem.style.display = 'inline-block';

            cropperModal.hide();
        });

        function addService() {
            const id = genId();
            const container = document.getElementById('services-container');
            const html = `
            <div class="service-box">
                <div class="d-flex justify-content-between mb-3">
                    <h5 class="text-secondary"><i class="fa-solid fa-layer-group"></i> Nova Área</h5>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.parentElement.parentElement.remove()">Excluir</button>
                </div>
                <div class="row mt-2">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Título</label>
                        <input type="text" class="form-control" name="servico_titulo[]" required>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label fw-bold">Descrição Breve</label>
                        <input type="text" class="form-control" name="servico_descricao[]">
                    </div>
                    <div class="col-md-12 mb-4">
                        <label class="form-label fw-bold">Imagem do Serviço</label>
                        <div class="row align-items-center">
                            <div class="col-md-4">
                                <div class="img-preview-box m-0">
                                    <img id="preview_svc_${id}" src="" style="display: none;">
                                </div>
                            </div>
                            <div class="col-md-8">
                                <input type="hidden" name="servico_imagem[]" id="input_svc_${id}">
                                <button type="button" class="btn btn-outline-primary" onclick="triggerCrop('input_svc_${id}', 'preview_svc_${id}', 16/9)"><i class="fa-solid fa-upload"></i> Escolher Foto do Computador</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label fw-bold text-success">Texto do Botão</label>
                        <input type="text" class="form-control border-success" name="servico_link_texto[]" placeholder="Ex: Agendar Sessão">
                    </div>
                    <div class="col-md-8 mb-2">
                        <label class="form-label fw-bold text-success">Mensagem Automática (WhatsApp)</label>
                        <input type="text" class="form-control border-success" name="servico_whatsapp_msg[]" placeholder="Ex: Olá, quero falar sobre...">
                    </div>
                </div>
            </div>`;
            container.insertAdjacentHTML('beforeend', html);
        }
    </script>
</body>
</html>
