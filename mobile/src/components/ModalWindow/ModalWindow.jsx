import * as React from 'react';
import { View, Modal } from "react-native";

export const ModalWindow = ({state, children}) => {
  return (
    <Modal visible={state}>
      <View style={styles.modal}>
        {
          children
        }
      </View>
    </Modal>
  )
};

const styles = {
  modal: {
    backgroundColor: '#9ed4e480',
    flex: 1,
    padding: 16,
  },
}
