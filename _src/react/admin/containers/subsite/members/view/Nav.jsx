import React, { Component } from 'react';
import { Link } from 'react-router-dom';
import PropTypes from 'prop-types';

export function Nav(props) {
	const { active_nav, hammock, member_id } = props;
	const strings = hammock.strings;
    return (
        <div>
            <h2 className="uk-heading-divider">{strings.edit.title}</h2>
            <Link className="uk-border-rounded uk-margin-bottom uk-background-default uk-button uk-button-default uk-button-small" to="/">{strings.edit.back}</Link>
            <ul className="uk-nav-default hammock-switcher-nav uk-nav-parent-icon" uk-nav="">
				<li className={active_nav === 'details' ? 'uk-active' : '' }>
                    <Link to={"/member/" + member_id} className="hammock-nav-button uk-text-left uk-border-rounded uk-box-shadow-small uk-background-default uk-button uk-button-default uk-button-small"><span className="uk-margin-left">{strings.edit.tabs.details}</span></Link>
                </li>
                <li className={active_nav === 'activity' ? 'uk-active' : '' }>
                    <Link to={"/member/" + member_id + "/activity"} className="hammock-nav-button uk-text-left uk-border-rounded uk-box-shadow-small uk-background-default uk-button uk-button-default uk-button-small"><span className="uk-margin-left">{strings.edit.tabs.activity}</span></Link>
                </li>
				<li className={active_nav === 'transactions' ? 'uk-active' : '' }>
                    <Link to={"/member/" + member_id + "/transactions"} className="hammock-nav-button uk-text-left uk-border-rounded uk-box-shadow-small uk-background-default uk-button uk-button-default uk-button-small"><span className="uk-margin-left">{strings.edit.tabs.transactions}</span></Link>
                </li>
            </ul>
        </div>
    );
}