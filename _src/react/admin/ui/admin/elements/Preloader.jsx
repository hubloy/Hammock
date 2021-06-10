import React from 'react';

export function Preloader(props) {
	const { text = window.hammock.common.status.loading } = props;
	return (
		<div className="hammock-preloader">
			<div className="content">
				<div uk-spinner=""></div><br/>
				<span>{text}</span>
			</div>
		</div>
	)
}