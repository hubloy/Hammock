<?php
/**
 * Email Footer
 *
 * This template can be overridden by copying it to yourtheme/hammock/emails/email-footer.php.
 * 
 * @package Hammock/Templates/Emails
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
</div>
														</td>
													</tr>
												</table>
												<!-- End Content -->
											</td>
										</tr>
									</table>
									<!-- End Body -->
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td align="center" valign="top">
						<!-- Footer -->
						<table border="0" cellpadding="10" cellspacing="0" width="600" id="template_footer">
							<tr>
								<td valign="top">
									<table border="0" cellpadding="10" cellspacing="0" width="100%">
										<tr>
											<td colspan="2" valign="middle" id="credit">
												<?php echo wp_kses_post( wpautop( wptexturize( apply_filters( 'hammock_email_footer_text', get_bloginfo( 'name', 'display' ) ) ) ) ); ?>
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
						<!-- End Footer -->
					</td>
				</tr>
			</table>
		</div>
	</body>
</html>
