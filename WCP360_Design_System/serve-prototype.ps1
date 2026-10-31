param([ValidateRange(1024,65535)][int]$Port = 8790)

$ErrorActionPreference = 'Stop'
$prototypeRoot = [IO.Path]::GetFullPath($PSScriptRoot)
$rootPrefix = $prototypeRoot + [IO.Path]::DirectorySeparatorChar
$listener = New-Object Net.Sockets.TcpListener([Net.IPAddress]::Loopback, $Port)
$mimeTypes = @{'.html'='text/html; charset=utf-8'; '.css'='text/css; charset=utf-8'; '.js'='text/javascript; charset=utf-8'; '.json'='application/json; charset=utf-8'; '.md'='text/plain; charset=utf-8'; '.ico'='image/x-icon'; '.png'='image/png'; '.svg'='image/svg+xml'}

try {
  $listener.Start()
  Write-Host "WCP360 : http://127.0.0.1:$Port/"
  Write-Host 'Gardez cette fenetre ouverte. Ctrl+C pour arreter le serveur.'
  while ($true) {
    $client = $listener.AcceptTcpClient()
    try {
      $stream = $client.GetStream()
      $stream.ReadTimeout = 3000
      $stream.WriteTimeout = 3000
      $reader = New-Object IO.StreamReader($stream, [Text.Encoding]::ASCII, $false, 1024, $true)
      $request = $reader.ReadLine()
      if (-not $request) { continue }
      $headerCount = 0
      while ($reader.ReadLine() -ne '') { if (++$headerCount -gt 100) { throw 'Too many headers' } }
      $parts = $request.Split(' ')
      $method = $parts[0]
      $route = [Uri]::UnescapeDataString(($parts[1] -split '[?#]')[0])
      if ($route -eq '/') { $route = '/index.html' }
      $target = [IO.Path]::GetFullPath((Join-Path $prototypeRoot $route.TrimStart('/').Replace('/', '\')))
      $status = '200 OK'
      $contentType = 'text/plain; charset=utf-8'
      $payload = [Text.Encoding]::UTF8.GetBytes('Not found')
      if ($method -notin @('GET','HEAD')) {
        $status = '405 Method Not Allowed'
        $payload = [Text.Encoding]::UTF8.GetBytes('Only GET and HEAD are supported')
      } elseif (-not $target.StartsWith($rootPrefix, [StringComparison]::OrdinalIgnoreCase) -or -not [IO.File]::Exists($target)) {
        $status = '404 Not Found'
      } else {
        $payload = [IO.File]::ReadAllBytes($target)
        $extension = [IO.Path]::GetExtension($target).ToLowerInvariant()
        if ($mimeTypes.ContainsKey($extension)) { $contentType = $mimeTypes[$extension] }
      }
      $headers = [Text.Encoding]::ASCII.GetBytes("HTTP/1.1 $status`r`nContent-Type: $contentType`r`nContent-Length: $($payload.Length)`r`nConnection: close`r`nCache-Control: no-cache`r`nX-Content-Type-Options: nosniff`r`n`r`n")
      $stream.Write($headers, 0, $headers.Length)
      if ($method -ne 'HEAD') { $stream.Write($payload, 0, $payload.Length) }
      $stream.Flush()
    } catch {
      Write-Verbose $_.Exception.Message
    } finally {
      if ($reader) { $reader.Dispose(); $reader = $null }
      $client.Close()
    }
  }
} finally {
  $listener.Stop()
}
