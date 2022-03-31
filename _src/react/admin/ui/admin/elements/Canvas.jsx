import React, { PureComponent } from 'react';

export class Canvas extends PureComponent {

	render() {
        const { canvas_id } = this.props;
        return (
            <div id={canvas_id} className={"hubloy_membership-offcanvas-flip " + canvas_id} uk-offcanvas="flip: true;">
				<div className="uk-offcanvas-bar uk-box-shadow-small">
					<button className="uk-offcanvas-close" type="button" uk-close=""></button>
					<div className="uk-padding uk-padding-remove-left uk-padding-remove-right uk-padding-remove-bottom hubloy_membership-canvas-content">
						{this.props.children}
					</div>
				</div>
			</div>
        )
    }
}