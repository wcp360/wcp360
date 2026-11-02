param(
  [switch]$Strict
)

$ErrorActionPreference = "Stop"

$workspace = Resolve-Path (Join-Path $PSScriptRoot "..")
$failures = New-Object System.Collections.Generic.List[object]
$warnings = New-Object System.Collections.Generic.List[string]

function Add-Failure {
  param(
    [string]$Category,
    [string]$File,
    [string]$Message
  )

  $failures.Add([pscustomobject]@{
    Category = $Category
    File = $File
    Message = $Message
  })
}

function Add-Warning {
  param([string]$Message)
  $warnings.Add($Message)
}

function Test-HtmlLocalReferences {
  $roots = @("WCP360_Prototype", "dashboard-html-css-javascript")
  $pattern = '(?:href|src)=["'']([^"'']+)["'']'
  $checked = 0

  foreach ($root in $roots) {
    $rootPath = Join-Path $workspace $root
    if (-not (Test-Path -LiteralPath $rootPath)) {
      Add-Failure "structure" $root "Missing expected HTML root."
      continue
    }

    Get-ChildItem -LiteralPath $rootPath -Filter *.html -File -Recurse | ForEach-Object {
      $file = $_
      $content = Get-Content -Raw -Encoding UTF8 -LiteralPath $file.FullName

      foreach ($match in [regex]::Matches($content, $pattern)) {
        $url = $match.Groups[1].Value.Trim()
        if ($url -eq "" -or $url -match "^(https?:|mailto:|tel:|#|javascript:|data:)") {
          continue
        }

        $local = ($url -split "[?#]")[0]
        if ($local -eq "" -or $local.StartsWith("#")) {
          continue
        }

        $target = Join-Path -Path $file.DirectoryName -ChildPath $local
        $checked++

        if (-not (Test-Path -LiteralPath $target)) {
          Add-Failure "html-local-reference" (Resolve-Path -Relative $file.FullName) "Missing local reference: $url"
        }
      }
    }
  }

  return $checked
}

function Test-MarkdownIndexReferences {
  $checks = @(
    @{ Base = "docs"; File = "docs\INDEX.md" },
    @{ Base = "docs\WCP360_CONSOLIDATED_MD_2026-09-27"; File = "docs\WCP360_CONSOLIDATED_MD_2026-09-27\00-MASTER-INDEX.md" }
  )

  $checked = 0

  foreach ($check in $checks) {
    $indexPath = Join-Path $workspace $check.File
    if (-not (Test-Path -LiteralPath $indexPath)) {
      Add-Failure "markdown-index" $check.File "Missing index file."
      continue
    }

    $basePath = Join-Path $workspace $check.Base
    $content = Get-Content -Raw -Encoding UTF8 -LiteralPath $indexPath

    foreach ($match in [regex]::Matches($content, '`([^`]+\.(?:md|html|txt|csv))`')) {
      $ref = $match.Groups[1].Value
      if ($ref -match "^(https?:|mailto:)") {
        continue
      }

      $target = Join-Path -Path $basePath -ChildPath $ref
      $checked++

      if (-not (Test-Path -LiteralPath $target)) {
        Add-Failure "markdown-index-reference" $check.File "Missing referenced file: $ref"
      }
    }
  }

  return $checked
}

function Test-WorkspaceShape {
  $expected = @(
    "docs",
    "WCP360_Prototype",
    "dashboard-html-css-javascript",
    "WCP360 Official html website",
    "WCP360 Source Code"
  )

  foreach ($name in $expected) {
    $path = Join-Path $workspace $name
    if (-not (Test-Path -LiteralPath $path -PathType Container)) {
      Add-Failure "structure" $name "Missing expected directory."
      continue
    }

    $fileCount = (Get-ChildItem -LiteralPath $path -File -Recurse -ErrorAction SilentlyContinue | Measure-Object).Count
    if ($fileCount -eq 0 -and ($name -eq "WCP360 Official html website" -or $name -eq "WCP360 Source Code")) {
      Add-Warning "$name is reserved but empty."
    }
  }
}

function Test-BaselineSignals {
  $statusFile = Join-Path $workspace "docs\WCP360_CONSOLIDATED_MD_2026-09-27\governance\01-BASELINE-STATUS.md"
  $designRoot = Join-Path $workspace "WCP360_Prototype"

  if (-not (Test-Path -LiteralPath $statusFile)) {
    return
  }

  $status = Get-Content -Raw -Encoding UTF8 -LiteralPath $statusFile
  $declaredDesign = $null

  if ($status -match '\| Design System \| `([^`]+)`') {
    $declaredDesign = $Matches[1]
  }

  if ($declaredDesign) {
    $declaredDesignVersion = $declaredDesign -replace "^[Vv]", ""
    $localVersions = Get-ChildItem -LiteralPath $designRoot -File -Filter "v*-index.html" -ErrorAction SilentlyContinue |
      ForEach-Object {
        if ($_.BaseName -match "^v(.+)-index$") { $Matches[1] }
      }

    if ($localVersions -and ($declaredDesignVersion -notin $localVersions)) {
      $latestLocal = $localVersions | Sort-Object { [version]$_ } | Select-Object -Last 1
      Add-Warning "Declared design baseline $declaredDesign is not present as a local versioned HTML index. Latest local detected: $latestLocal."
    }
  }

  if ($status -match '\| Code \| `NONE`') {
    Add-Warning "Code baseline is NONE; implementation tests cannot exist yet."
  }
}

Write-Host "WCP360 static project checks"
Write-Host "Workspace: $workspace"
Write-Host ""

Test-WorkspaceShape
$htmlRefs = Test-HtmlLocalReferences
$markdownRefs = Test-MarkdownIndexReferences
Test-BaselineSignals

Write-Host "Checked HTML local references: $htmlRefs"
Write-Host "Checked Markdown index references: $markdownRefs"
Write-Host "Warnings: $($warnings.Count)"
foreach ($warning in $warnings) {
  Write-Host "WARN: $warning"
}

Write-Host "Failures: $($failures.Count)"
if ($failures.Count -gt 0) {
  $failures | Format-Table -AutoSize
  exit 1
}

if ($Strict -and $warnings.Count -gt 0) {
  Write-Host "Strict mode treats warnings as failures."
  exit 1
}

Write-Host "OK"
exit 0
