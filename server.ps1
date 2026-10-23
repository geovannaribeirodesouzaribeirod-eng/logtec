# LogTec - Servidor Localhost em PowerShell
param(
    [int]$Port = 8000,
    [string]$Path = $PSScriptRoot
)

$listener = New-Object System.Net.HttpListener
$prefix = "http://localhost:$Port/"
$listener.Prefixes.Add($prefix)

try {
    $listener.Start()
} catch {
    $prefix = "http://127.0.0.1:$Port/"
    $listener = New-Object System.Net.HttpListener
    $listener.Prefixes.Add($prefix)
    $listener.Start()
}

Write-Output "Servidor LogTec rodando em: $prefix"
Write-Output "Diretorio raiz: $Path"

$mimeTypes = @{
    ".html" = "text/html; charset=utf-8"
    ".htm"  = "text/html; charset=utf-8"
    ".css"  = "text/css; charset=utf-8"
    ".js"   = "application/javascript; charset=utf-8"
    ".json" = "application/json; charset=utf-8"
    ".png"  = "image/png"
    ".jpg"  = "image/jpeg"
    ".jpeg" = "image/jpeg"
    ".svg"  = "image/svg+xml"
    ".ico"  = "image/x-icon"
    ".txt"  = "text/plain; charset=utf-8"
    ".md"   = "text/markdown; charset=utf-8"
    ".sql"  = "text/plain; charset=utf-8"
    ".php"  = "text/html; charset=utf-8"
}

while ($listener.IsListening) {
    try {
        $context = $listener.GetContext()
        $request = $context.Request
        $response = $context.Response

        $urlPath = $request.Url.LocalPath.TrimStart('/')
        if ([string]::IsNullOrWhiteSpace($urlPath)) {
            $urlPath = "index.html"
        }

        # Converte separadores de caminho
        $localFile = [System.IO.Path]::Combine($Path, $urlPath.Replace('/', [System.IO.Path]::DirectorySeparatorChar))

        if (Test-Path -Path $localFile -PathType Leaf) {
            $ext = [System.IO.Path]::GetExtension($localFile).ToLower()
            $mime = "application/octet-stream"
            if ($mimeTypes.ContainsKey($ext)) {
                $mime = $mimeTypes[$ext]
            }

            $response.ContentType = $mime
            $response.Headers.Add("Access-Control-Allow-Origin", "*")
            $response.StatusCode = 200

            $buffer = [System.IO.File]::ReadAllBytes($localFile)
            $response.ContentLength64 = $buffer.Length
            $response.OutputStream.Write($buffer, 0, $buffer.Length)
        } else {
            # Se for pedido arquivo .php ou inexistente, redireciona para index.html para simulação
            if (Test-Path -Path (Join-Path $Path "index.html")) {
                $buffer = [System.IO.File]::ReadAllBytes((Join-Path $Path "index.html"))
                $response.ContentType = "text/html; charset=utf-8"
                $response.StatusCode = 200
                $response.ContentLength64 = $buffer.Length
                $response.OutputStream.Write($buffer, 0, $buffer.Length)
            } else {
                $response.StatusCode = 404
                $msg = [System.Text.Encoding]::UTF8.GetBytes("Arquivo nao encontrado: $urlPath")
                $response.ContentLength64 = $msg.Length
                $response.OutputStream.Write($msg, 0, $msg.Length)
            }
        }
        $response.Close()
    } catch {
        # Ignora excecoes pontuais de desconexao do cliente
    }
}
