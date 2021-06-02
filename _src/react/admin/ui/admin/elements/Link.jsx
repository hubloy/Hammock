import React, { Component } from 'react';
import PropTypes from 'prop-types';

export function LinkUI(props) {
	const { title, href = '', target = '', rel = '', class_name = '', placeholder = '', attributes = Array() } = props;
	var data = Array();
	for (var k in attributes) {
		if (attributes.hasOwnProperty(k)) {
			data.push(k + "=" + attributes[k]);
		}
	}
	data = data.join(" ");
	return (
		<a className={class_name} href={href} {...target} {...rel} {...data} title={title}>{title}</a>
	)
}