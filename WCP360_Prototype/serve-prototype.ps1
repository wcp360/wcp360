param([ValidateRange(1024,65535)][int]$Port = 8790)
$ErrorActionPreference = 'Stop'
$prototypeRoot = [IO.Path]::GetFullPath($PSScriptRoot)
$rootPrefix = $prototypeRoot + [IO.Path]::DirectorySeparatorChar
$listener = New-Object Net.Sockets.TcpListener([Net.IPAddress]::Loopback, $Port)
$mimeTypes = @{'.html'='text/html; charset=utf-8'; '.css'='text/css; charset=utf-8'; '.js'='text/javascript; charset=utf-8'; '.json'='application/json; charset=utf-8'; '.md'='text/plain; charset=utf-8'; '.ico'='image/x-icon'; '.png'='image/png'; '.svg'='image/svg+xml'; '.webp'='image/webp'; '.woff2'='font/woff2'}
function Send-PreviewResponse($Stream, $Method, $Status, $Payload, $ContentType) {
  $allow = if ($Status.StartsWith('405')) { "Allow: GET, HEAD`r`n" } else { '' }
  $headers = [Text.Encoding]::ASCII.GetBytes("HTTP/1.1 $Status`r`nContent-Type: $ContentType`r`nContent-Length: $($Payload.Length)`r`nConnection: close`r`nCache-Control: no-store`r`nX-Content-Type-Options: nosniff`r`n$allow`r`n")
  $Stream.Write($headers, 0, $headers.Length)
  if ($Method -ne 'HEAD') { $Stream.Write($Payload, 0, $Payload.Length) }
  $Stream.Flush()
}
try {
  $listener.Start()
  Write-Host "WCP360 prototype: http://127.0.0.1:$Port/"
  Write-Host 'Keep this window open. Ctrl+C stops the preview.'
  while ($true) {
    $client = $listener.AcceptTcpClient()
    $stream = $null
    $reader = $null
    $method = 'GET'
    try {
      $stream = $client.GetStream()
      $stream.ReadTimeout = 3000
      $stream.WriteTimeout = 3000
      $reader = New-Object IO.StreamReader($stream, [Text.Encoding]::ASCII, $false, 1024, $true)
      $request = $reader.ReadLine()
      if (-not $request) { continue }
      if ($request.Length -gt 8192 -or $request -notmatch '^[A-Z]+ /[^ ]* HTTP/1\.[01]$') { throw 'Malformed request' }
      $headerCount = 0
      do {
        $header = $reader.ReadLine()
        $headerCount++
        if ($null -eq $header -or $header.Length -gt 8192 -or $headerCount -gt 100) { throw 'Malformed headers' }
      } while ($header -ne '')
      $parts = $request.Split(' ')
      $method = $parts[0]
      if ($parts[1] -match '%(?![0-9a-fA-F]{2})') { throw 'Malformed URL' }
      $route = [Uri]::UnescapeDataString(($parts[1] -split '[?#]')[0])
      if ($route -eq '/') { $route = '/index.html' }
      $status = '404 Not Found'
      $contentType = 'text/plain; charset=utf-8'
      $payload = [Text.Encoding]::UTF8.GetBytes('Not found')
      if ($method -notin @('GET','HEAD')) {
        $status = '405 Method Not Allowed'
        $payload = [Text.Encoding]::UTF8.GetBytes('Only GET and HEAD are supported')
      } elseif (-not $route.Contains('\') -and -not $route.Contains([char]0) -and -not ($route.Split('/') | Where-Object { $_.StartsWith('.') })) {
        $target = [IO.Path]::GetFullPath((Join-Path $prototypeRoot $route.TrimStart('/').Replace('/', '\')))
        $extension = [IO.Path]::GetExtension($target).ToLowerInvariant()
        if ($target.StartsWith($rootPrefix, [StringComparison]::OrdinalIgnoreCase) -and [IO.File]::Exists($target) -and $mimeTypes.ContainsKey($extension)) {
          $currentPath = $target
          $linkedPath = $false
          while ($currentPath -ne $prototypeRoot) {
            if (([IO.File]::GetAttributes($currentPath) -band [IO.FileAttributes]::ReparsePoint) -ne 0) { $linkedPath = $true; break }
            $currentPath = [IO.Path]::GetDirectoryName($currentPath)
          }
          if (-not $linkedPath) {
            $status = '200 OK'
            $payload = [IO.File]::ReadAllBytes($target)
            $contentType = $mimeTypes[$extension]
          }
        }
      }
      Send-PreviewResponse $stream $method $status $payload $contentType
    } catch {
      Write-Verbose $_.Exception.Message
      if ($stream -and $stream.CanWrite) {
        try { Send-PreviewResponse $stream $method '400 Bad Request' ([Text.Encoding]::ASCII.GetBytes('Bad request')) 'text/plain; charset=utf-8' } catch { Write-Verbose 'Client disconnected' }
      }
    } finally {
      if ($reader) { $reader.Dispose() }
      $client.Close()
    }
  }
} finally { $listener.Stop() }
