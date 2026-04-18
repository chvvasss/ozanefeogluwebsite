<?php

declare(strict_types=1);

namespace App\Services\Content;

use Mews\Purifier\Facades\Purifier;

/**
 * Sanitizes rich-text body HTML coming from the admin editor.
 * Narrow safelist — only tags the public prose-article style supports.
 */
class BodySanitizer
{
    private const CONFIG = [
        'HTML.Allowed' => 'p,br,strong,em,u,s,sub,sup,'
            .'h2,h3,h4,'
            .'ul,ol,li,'
            .'blockquote,cite,'
            .'a[href|target|rel|title],'
            .'code,pre,'
            .'hr,'
            .'img[src|alt|width|height],'
            .'figure,figcaption',
        'HTML.TargetBlank'   => true,
        'HTML.Nofollow'      => false,
        'Attr.AllowedFrameTargets' => ['_blank'],
        'URI.AllowedSchemes' => ['http' => true, 'https' => true, 'mailto' => true],
        'AutoFormat.RemoveEmpty' => true,
        'Core.EscapeInvalidTags' => false,
        'HTML.Trusted'           => false,
    ];

    public static function clean(string $html): string
    {
        if ($html === '') {
            return '';
        }

        return (string) Purifier::clean($html, self::CONFIG);
    }
}
