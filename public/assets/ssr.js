'use strict';

class Render extends React.Component {
    constructor(props) {
        super(props);
    }

    render() {
        return React.createElement(
            'div',
            {},
            this.props.items.map((item, k) => {
                return React.createElement(item.name, {}, item.value);
            })
        );
    }
}
