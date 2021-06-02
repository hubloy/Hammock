import React, { Component } from 'react';
import PropTypes from 'prop-types';

export function ToggleInfoBox(props) {
	const { title, icon, linkText = '', linkTo = '' } = props;
    return (
        <div className="infobox">
			<div className="info-icon">
				<span uk-icon={"icon: "+icon+"; ratio: 2"}></span>
			</div>
			<h5 className="info-heading">{title}</h5>
			<a className="info-link uk-button uk-button-small" href={linkTo} uk-toggle="">{linkText} <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" className="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg></a>
		</div>
    );
}