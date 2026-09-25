<?php
/**
 * SenMoringa — Bibliothèque d'icônes SVG maison.
 * Pas de librairie d'icônes générique : tout est dessiné pour le site.
 */

function svgLogo(): string
{
    return '<svg viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M20 4C20 4 8 10 8 22C8 30 13 36 20 36C27 36 32 30 32 22C32 10 20 4 20 4Z" fill="currentColor" opacity="0.15"/>
        <path d="M20 6C20 6 10 12 10 22C10 28.5 14.5 34 20 34" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        <path d="M20 6C20 6 30 12 30 22C30 28.5 25.5 34 20 34" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        <path d="M20 34V14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
    </svg>';
}

function svgVigneDiviseur(): string
{
    return '<svg class="diviseur-vigne" viewBox="0 0 1160 46" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
        <path d="M0 23 C 150 5, 300 41, 450 23 S 750 5, 900 23 S 1050 41, 1160 23" stroke-width="1.5" fill="none" stroke-linecap="round"/>
        <g fill="#7FB35C">
            <ellipse cx="150" cy="12" rx="9" ry="5" transform="rotate(-25 150 12)"/>
            <ellipse cx="450" cy="34" rx="9" ry="5" transform="rotate(20 450 34)"/>
            <ellipse cx="750" cy="12" rx="9" ry="5" transform="rotate(-20 750 12)"/>
            <ellipse cx="1010" cy="34" rx="9" ry="5" transform="rotate(20 1010 34)"/>
        </g>
    </svg>';
}

function svgIconeBienfait(string $type): string
{
    $icones = [
        'nutrition' => '<path d="M12 3v6M8 6l4-3 4 3M6 21c0-5 2-9 6-9s6 4 6 9" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" fill="none"/>',
        'energie' => '<path d="M13 2 4 14h7l-1 8 9-12h-7l1-8Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round" fill="none"/>',
        'peau' => '<circle cx="12" cy="12" r="8.5" stroke="currentColor" stroke-width="1.7" fill="none"/><path d="M8.5 12a3.5 3.5 0 0 1 7 0c0 2-1.5 3-3.5 5-2-2-3.5-3-3.5-5Z" stroke="currentColor" stroke-width="1.4" fill="none"/>',
        'immunite' => '<path d="M12 3l7 3v6c0 5-3 8-7 9-4-1-7-4-7-9V6l7-3Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round" fill="none"/><path d="M9 12l2 2 4-4" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" fill="none"/>',
    ];
    $inner = $icones[$type] ?? $icones['nutrition'];
    return '<svg width="26" height="26" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">' . $inner . '</svg>';
}

function svgIconeCategorie(string $slug): string
{
    $icones = [
        'poudre' => '<circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.5" fill="none"/><path d="M8 15c1-3 2-5 4-5s3 2 4 5" stroke="currentColor" stroke-width="1.4" fill="none" stroke-linecap="round"/>',
        'huile' => '<path d="M12 3c3 4 6 7.5 6 11.5A6 6 0 0 1 6 14.5C6 10.5 9 7 12 3Z" stroke="currentColor" stroke-width="1.6" fill="none"/>',
        'jus' => '<path d="M8 3h8l-1 5H9L8 3Z" stroke="currentColor" stroke-width="1.5" fill="none" stroke-linejoin="round"/><path d="M9 8l1 12h4l1-12" stroke="currentColor" stroke-width="1.5" fill="none" stroke-linejoin="round"/>',
        'complements' => '<rect x="7" y="4" width="10" height="16" rx="5" stroke="currentColor" stroke-width="1.5" fill="none"/><path d="M7 12h10" stroke="currentColor" stroke-width="1.5"/>',
    ];
    $inner = $icones[$slug] ?? $icones['poudre'];
    return '<svg class="icone-cat" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">' . $inner . '</svg>';
}

/**
 * Vignette générée pour un produit sans photo, selon sa catégorie.
 * Permet au site d'être présentable dès la première installation,
 * avant l'ajout de vraies photos via l'espace admin.
 */
function svgVignetteProduit(?string $categorieSlug): string
{
    $couleur = match ($categorieSlug) {
        'huile' => '#A2622B',
        'jus' => '#4F8B3B',
        'complements' => '#6B5230',
        default => '#23421F',
    };
    $formes = [
        'poudre' => '<rect x="30" y="18" width="40" height="58" rx="6" stroke="' . $couleur . '" stroke-width="2.2" fill="none"/><path d="M38 34h24M38 44h24M38 54h16" stroke="' . $couleur . '" stroke-width="2" stroke-linecap="round"/><path d="M42 18v-6h16v6" stroke="' . $couleur . '" stroke-width="2.2" fill="none"/>',
        'huile' => '<path d="M50 12c9 12 18 22 18 33a18 18 0 1 1-36 0c0-11 9-21 18-33Z" stroke="' . $couleur . '" stroke-width="2.2" fill="none"/><path d="M50 34c4 5 9 10 9 15a9 9 0 1 1-18 0c0-5 5-10 9-15Z" fill="' . $couleur . '" opacity="0.18"/>',
        'jus' => '<path d="M38 14h24l-3 14H41l-3-14Z" stroke="' . $couleur . '" stroke-width="2.2" fill="none" stroke-linejoin="round"/><path d="M41 28l3 46h12l3-46" stroke="' . $couleur . '" stroke-width="2.2" fill="none" stroke-linejoin="round"/><path d="M43 40h14" stroke="' . $couleur . '" stroke-width="1.6" stroke-linecap="round"/>',
        'complements' => '<rect x="32" y="10" width="36" height="60" rx="16" stroke="' . $couleur . '" stroke-width="2.2" fill="none"/><path d="M32 40h36" stroke="' . $couleur . '" stroke-width="2.2"/>',
    ];
    $forme = $formes[$categorieSlug] ?? $formes['poudre'];
    return '<svg viewBox="0 0 100 90" fill="none" xmlns="http://www.w3.org/2000/svg">' . $forme . '</svg>';
}

function svgFeuilleGrande(): string
{
    return '<svg viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M100 20c0 0-55 30-55 85 0 38 25 65 55 65s55-27 55-65c0-55-55-85-55-85Z" stroke="#FBF8F1" stroke-width="2.5" fill="none" opacity="0.9"/>
        <path d="M100 30v130" stroke="#FBF8F1" stroke-width="2" stroke-linecap="round" opacity="0.9"/>
        <path d="M100 55 78 68M100 55 122 68M100 80 74 95M100 80 126 95M100 105 76 120M100 105 124 120M100 130 80 143M100 130 120 143" stroke="#FBF8F1" stroke-width="1.6" stroke-linecap="round" opacity="0.75"/>
    </svg>';
}

function svgPanierIcone(): string
{
    return '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3 4h2l2.4 12.2A2 2 0 0 0 9.36 18H18a2 2 0 0 0 1.96-1.6L21.5 8H6.2" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/><circle cx="10" cy="21" r="1.4" fill="currentColor"/><circle cx="18" cy="21" r="1.4" fill="currentColor"/></svg>';
}

function svgPanierVideIllustration(): string
{
    return '<svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15 20h10l12 55a8 8 0 0 0 8 6h32a8 8 0 0 0 7.8-6.2L92 34H31" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"/><circle cx="42" cy="90" r="5" fill="currentColor"/><circle cx="72" cy="90" r="5" fill="currentColor"/></svg>';
}

function svgIconeReseau(string $type): string
{
    $icones = [
        'linkedin' => '<path d="M6.9 8.5H3.6V20h3.3V8.5ZM5.25 3.5a2 2 0 1 0 0 4 2 2 0 0 0 0-4ZM20.4 20h-3.3v-6.1c0-1.5-.03-3.4-2.1-3.4-2.1 0-2.4 1.6-2.4 3.3V20h-3.3V8.5h3.17v1.57h.05c.44-.83 1.53-1.7 3.15-1.7 3.36 0 4 2.2 4 5.1V20Z" fill="currentColor"/>',
        'instagram' => '<rect x="3" y="3" width="18" height="18" rx="5" stroke="currentColor" stroke-width="1.7" fill="none"/><circle cx="12" cy="12" r="4.2" stroke="currentColor" stroke-width="1.7" fill="none"/><circle cx="17.4" cy="6.6" r="1.1" fill="currentColor"/>',
        'facebook' => '<path d="M14.5 8.5h2V5.3h-2c-2.2 0-3.8 1.6-3.8 3.9v1.9H8.5v3.2h2.2V21h3.2v-6.7h2.3l.4-3.2h-2.7V9.6c0-.7.3-1.1 1.1-1.1Z" fill="currentColor"/>',
        'tiktok' => '<path d="M16.6 3h-3.1v12.4a2.6 2.6 0 1 1-1.9-2.5V9.7a5.7 5.7 0 1 0 5 5.7V9.2a7.7 7.7 0 0 0 4.4 1.4V7.5a4.6 4.6 0 0 1-4.4-4.5Z" fill="currentColor"/>',
    ];
    $inner = $icones[$type] ?? $icones['facebook'];
    return '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">' . $inner . '</svg>';
}

function svgCoche(): string
{
    return '<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.6"/><path d="M8 12.5l2.5 2.5L16 9.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>';
}
