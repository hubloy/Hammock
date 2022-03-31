import React from 'react';

export function Center(props) {
	const { text, className = "", backgroundImage = "" } = props;
	return (
		<div className="hubloy_membership-center hubloy_membership-background-contain" style={{backgroundImage : "url('"+backgroundImage+"')"}}>
			<div className="center-content">
				<span className={className}>{text}</span>
			</div>
		</div>
	)
}