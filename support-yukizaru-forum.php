<?php
/**
 * Plugin name: 雪猿掲示板を救え！
 * Description: Snow Monkey 公式フォーラムにカキコするときにお役立ちになりそうなダッシュボードウィジェット
 * Version: 0.1.0
 *
 * @package support-yukizaru-forum
 * @author kutsu
 * @license GPL-2.0+
 */

/**
 * Snow Monkey 以外のテーマを利用している場合は有効化しても反映されないようにする
 */
$theme = wp_get_theme();
if ( 'snow-monkey' !== $theme->template && 'snow-monkey/resources' !== $theme->template ) {
	return;
}

/**
 * ダッシュボードに指定の内容を表示
 */
add_action( 'wp_dashboard_setup', 'msm_dashboard_setup' );

function msm_dashboard_setup() {
	if ( wp_get_current_user('administrator') ) { //管理者権限のみ
		// 有効化されているプラグイン
		wp_add_dashboard_widget(
			'my-snow-monkey-dashboard-widget-active-plugins',
			__( 'Active Plugins', 'my-snow-monkey' ),
			'msm_view_dashboard_widget_active_plugins'
		);
		// 有効化されているテーマ
		wp_add_dashboard_widget(
			'my-snow-monkey-dashboard-widget-theme',
			__( 'Theme', 'my-snow-monkey' ),
			'msm_view_dashboard_widget_theme'
		);
	}
}

function msm_view_dashboard_widget_active_plugins() {
	$_active_plugins = get_option( 'active_plugins', array() );
	if ( is_multisite() ) {
		$_network_activated_plugins = array_keys( get_site_option( 'active_sitewide_plugins', array() ) );
		$_active_plugins            = array_merge( $_active_plugins, $_network_activated_plugins );
	}
	?>
<table>
	<tbody>
	<?php
	foreach ( $_active_plugins as $_plugin ) {
		$_plugin_data = get_plugin_data( WP_PLUGIN_DIR . '/' . $_plugin );
		$_dirname     = dirname( $_plugin );
		if ( ! empty( $_plugin_data['Name'] ) ) {
			$_plugin_name = esc_html( $_plugin_data['Name'] );
			$td_style     = ( false !== strpos( $_plugin_name, 'Snow Monkey' ) ) ? ' style="font-weight: bold; color: #e00;"' : '';
			?>
		<tr>
			<td<?php echo $td_style; ?>><?php echo $_plugin_name; ?></td>
			<td<?php echo $td_style; ?>><?php echo esc_html( $_plugin_data['Version'] ); ?></td>
		</tr>
			<?php
		}
	}
	?>
	</tbody>
</table>
	<?php
}

function msm_view_dashboard_widget_theme() {
	include_once ABSPATH . 'wp-admin/includes/theme-install.php';
	$_active_theme = wp_get_theme();
	if ( is_child_theme() ) {
		$_active_theme = wp_get_theme( $_active_theme->Template );
	}
	$_theme_version = $_active_theme->Version;
	?>
<table>
	<tbody>
		<tr>
			<td data-export-label="is Child Theme"><?php _e( 'is Child Theme', 'my-snow-monkey' ); ?>:</td>
			<td><?php echo is_child_theme() ? '子テーマ使用中' : '子テーマ未使用'; ?></td>
		</tr>
		<tr>
			<td data-export-label="Name"><?php _e( 'Name', 'my-snow-monkey' ); ?>:</td>
			<td style="font-weight: bold;color: #e00;"><?php echo esc_html( $_active_theme->Name ); ?></td>
		</tr>
		<tr>
			<td data-export-label="Version"><?php _e( 'Version', 'my-snow-monkey' ); ?>:</td>
			<td style="font-weight: bold;color: #e00;"><?php echo esc_html( $_theme_version ); ?></td>
		</tr>
	</tbody>
</table>
	<?php
}

