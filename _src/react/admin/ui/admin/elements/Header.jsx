import React, { Component } from 'react';
import PropTypes from 'prop-types';

export function Header(props) {

    return (
        <div className="uk-child-width-1-1@m uk-grid-match" uk-grid="">
            <div>
                <div className="uk-card uk-card-default uk-card-body uk-padding-remove">
                    <div className="uk-width-auto uk-margin-left">
                        <img className="" src={props.hubloy_membership.assets_url + "/img/header.svg"} width="150" height="50" uk-svg="" />
                    </div>
                    
                    <div className="uk-position-center-right uk-margin-right">
                        <div className="uk-button-group">
                            <a className="uk-button uk-button-primary uk-border-rounded" href={props.hubloy_membership.common.urls.dash_url}>{props.hubloy_membership.common.string.dashboard}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}