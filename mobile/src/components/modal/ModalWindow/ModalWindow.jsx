import React from 'react';
import { Image, View } from 'react-native';
import { magicModal } from 'react-native-magic-modal';
import { Button } from "react-native-paper";
import { ModalInput } from "../ModalInput";
import { ModalList } from "../ModalList";

export const ModalWindow = ({label}) => {
  return (
    <View style={styles.container}>
      <Button
        onPress={() => magicModal.hide('close button pressed')}
        style={styles.btn}
        icon={() => (
          <Image
            source={require('../../../../assets/close.webp')}
            style={{ width: 30, height: 29 }}
          />
        )} />
      <ModalInput label={label} />
      <ModalList />
    </View>
  );
};

const styles = {
  container: {
    backgroundColor: 'transparent',
    margin: 16,
    position: 'relative'
  },
  btn: {
    position: 'absolute',
    top: -50,
    right: -20
  },
}
