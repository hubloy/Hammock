import React from 'react';

export function Preloader(props) {
	const { text = window.hubloy_membership.common.status.loading } = props;
	return (
		<div className="hubloy_membership-preloader">
			<div className="content">
				<div uk-spinner=""></div><br/>
				<span>{text}</span>
			</div>
		</div>
	)
}