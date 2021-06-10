import React from 'react';

export function Center(props) {
	const { text, className = "" } = props;
	return (
		<div className="hammock-center">
			<div className="center-content">
				<span className={className}>{text}</span>
			</div>
		</div>
	)
}