import React from 'react';

export function Center(props) {
	const { text, className = "", backgroundImage = "" } = props;
	return (
		<div className="hammock-center hammock-background-contain" style={{backgroundImage : "url('"+backgroundImage+"')"}}>
			<div className="center-content">
				<span className={className}>{text}</span>
			</div>
		</div>
	)
}