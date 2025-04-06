<?php
/**
 * WordPress için Temel Yapılandırma Dosyası
 * 
 * Bu dosya WordPress kurulumu sırasında kullanılır ve temel yapılandırma
 * ayarlarını içerir. Dosya wp-config.php olarak kopyalanır ve gerekli
 * değerler doldurulur.
 * 
 * Dosya içeriği:
 * 1. Veritabanı bağlantı ayarları
 * 2. Güvenlik anahtarları ve tuzları
 * 3. Veritabanı tablo öneki
 * 4. WordPress dizin yolu (ABSPATH)
 * 5. Hata ayıklama modu ayarları
 */

// ** Veritabanı Ayarları ** //
/** WordPress veritabanı adı */
define( 'DB_NAME', 'wordpress' );

/** MariaDB kullanıcı adı - Docker ağında WordPress'in veritabanına erişimi için */
define( 'DB_USER', 'ctasar' );

/** MariaDB kullanıcı şifresi */
define( 'DB_PASSWORD', '12345' );

/** MariaDB sunucu adresi - Docker servis adı kullanılıyor */
define( 'DB_HOST', 'mariadb' );

/** Veritabanı karakter seti - Türkçe karakterler için UTF-8 */
define( 'DB_CHARSET', 'utf8' );

/** Veritabanı karşılaştırma tipi - varsayılan değer kullanılıyor */
define( 'DB_COLLATE', '' );

/**#@+
 * Güvenlik Anahtarları ve Tuzları
 *
 * Her anahtar benzersiz ve rastgele bir dize olmalıdır!
 * WordPress.org'un güvenlik anahtarı servisinden oluşturulabilir
 * {@link https://api.wordpress.org/secret-key/1.1/salt/}
 *
 * Bu değerler değiştirildiğinde tüm çerezler geçersiz olur ve
 * kullanıcıların yeniden giriş yapması gerekir
 */
define('AUTH_KEY',         '^j6=.Iz]bCe$g_q#4CHCHn eH.mICAH6d8jb+Ti8 <Yhuhne5`(D.^Wwx%zF> |U');
define('SECURE_AUTH_KEY',  'mxvu@6~Iqty!+veNt+<6aBA#baFf_j4qP@0s_#vrvWy,t9Y_TrKTpej)=NPb*r!_');
define('LOGGED_IN_KEY',    'Mj-~gnW5|WhY] *mXYLn9DLKWI[~*?xV{/+c(wZ&BiisO8^{I(>>_q[3#UPVp~jl');
define('NONCE_KEY',        'GvTVW(*}72nl+uFaa),8|YL~a{+<93)FZo+xm/dqM+RY[lim<x!Vh$Hs++Y2[GK-');
define('AUTH_SALT',        'G5G[k=#h]v.|SXJGf!P]V~,@LKaZ(](sq2yjVMvnBkAbze(Wl)e+($BK]860-`wn');
define('SECURE_AUTH_SALT', ' }(ERlq+srd&[Xn(+YG?#ue|7:S:4;!*Kn@w=8z:Y|YC:(==kQgXVhem}HGB =l-');
define('LOGGED_IN_SALT',   '854yB.<{$|>D/f@.]6]6M+dX5U9dIC+h!zIIh8(-mCLWF8DhFTr~`wF[^*nFl+x<');
define('NONCE_SALT',       '.?&sAx3~17%0k>q]S5Iq-<0~+~x?mJ`gUT $Ew-N=RHP}u;5IM!L,XvtQy>nJ%G_');

/**#@-*/

/**
 * WordPress Veritabanı Tablo Öneki
 *
 * Aynı veritabanında birden fazla WordPress kurulumu olabilir
 * Her kurulum için farklı önek kullanılmalıdır
 * Sadece harfler, rakamlar ve alt çizgi kullanılabilir
 *
 * WordPress kurulduktan sonra bu değerin değiştirilmesi
 * sitenin kurulu olmadığını düşünmesine neden olur
 */
$table_prefix = 'wp_';

/**
 * WordPress Hata Ayıklama Modu
 *
 * Geliştirme sırasında hataları görmek için true yapılabilir
 * Canlı ortamda güvenlik için false olmalıdır
 * 
 * Ek hata ayıklama sabitleri için WordPress belgelerini inceleyin:
 * {@link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/}
 */
define( 'WP_DEBUG', false );

// Buraya özel değerler eklenebilir

// Düzenleme buraya kadar! İyi yayınlar.

/** WordPress dizininin tam yolu */
if ( ! defined( 'ABSPATH' ) ) {
    define( 'ABSPATH', __DIR__ . '/' );
}

/** WordPress değişkenlerini ve dosyalarını yükle */
require_once ABSPATH . 'wp-settings.php';